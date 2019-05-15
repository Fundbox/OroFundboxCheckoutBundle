<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Oro\Bundle\LocaleBundle\Model\AddressInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Psr\Log\LoggerInterface;

class FundboxCheckoutMethod implements PaymentMethodInterface
{
    const TRANSACTION_TOKEN_KEY = 'fbx_transaction_token';

    /**
     * @var string[] Currencies codes in ISO-4217
     */
    private $supportedCurrencyCodes = ['USD'];

    /**
     * @var string[] Contry codes in ISO2
     */
    private $supportedContryCodes = ['US'];

    /**
     * @var FundboxCheckoutConfigInterface
     */
    protected $config;

    /**
     * @var FundboxCheckoutTransport
     */
    protected $fundboxCheckoutTransport;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param FundboxCheckoutConfigInterface $config
     * @param FundboxCheckoutTransport $fundboxCheckoutTransport
     * @param LoggerInterface $logger
     */
    public function __construct(
        FundboxCheckoutConfigInterface $config,
        FundboxCheckoutTransport $fundboxCheckoutTransport,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->fundboxCheckoutTransport = $fundboxCheckoutTransport;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($action, PaymentTransaction $paymentTransaction)
    {
        $this->logger->debug(sprintf('FBX: payment method execute action: "%s"', $action));
        if (!$this->supports($action)) {
            throw new \InvalidArgumentException(sprintf('FBX: Unsupported action "%s"', $action));
        }
        $paymentTransaction->setAction($action);
        return $this->{$action}($paymentTransaction) ?: [];
    }

    public function purchase(PaymentTransaction $paymentTransaction)
    {
        $this->logger->debug('FBX: executing purchase');
        $paymentTransaction
            ->setActive(true) // The payment transaction is strarted
            ->setSuccessful(false); // The payment transaction isn't finished yet. Should be captured to be finished
    }

    public function capture(PaymentTransaction $paymentTransaction)
    {
        $this->logger->debug('FBX: executing capture');

        $sourcePaymentTransaction = $paymentTransaction->getSourcePaymentTransaction();
        if ($sourcePaymentTransaction !== null && $sourcePaymentTransaction->getAction() === self::AUTHORIZE) {
            $this->logger->debug('FBX: processing capture for authorized transaction');
            $response = $sourcePaymentTransaction->getResponse();
        } else {
            $response = $paymentTransaction->getResponse();
        }

        if (!array_key_exists(self::TRANSACTION_TOKEN_KEY, $response)) {
            throw new \InvalidArgumentException('FBX: Tried to capture without transaction token');
        }

        $transactionToken = $response[self::TRANSACTION_TOKEN_KEY];
        $paymentTransaction->setReference($transactionToken);

        $amountCents = $this->getAmountCents($paymentTransaction->getAmount());
        $this->logger->debug('FBX: applying order');
        $this->fundboxCheckoutTransport->applyOrder($transactionToken, $amountCents);
        $this->logger->debug('FBX: applied order successfully');

        if($sourcePaymentTransaction) {
            $this->finishSuccessfulTransaction($sourcePaymentTransaction);
            $this->logger->debug('FBX: finished authorized transaction');
        }
        $this->finishSuccessfulTransaction($paymentTransaction);

        $this->logger->debug('FBX: finished payment transction');
    }

    public function authorize(PaymentTransaction $paymentTransaction)
    {
        $this->logger->debug('FBX: executing authorize');
        $response = $paymentTransaction->getResponse();
        if (!array_key_exists(self::TRANSACTION_TOKEN_KEY, $response)) {
            throw new \InvalidArgumentException('FBX: Tried to authorize without transaction token');
        }
        $transactionToken = $response[self::TRANSACTION_TOKEN_KEY];
        $paymentTransaction->setReference($transactionToken);

        $amountCents = $this->getAmountCents($paymentTransaction->getAmount());
        $this->logger->debug('FBX: authorizing order');
        $this->fundboxCheckoutTransport->authorizeOrder($transactionToken, $amountCents);
        $this->logger->debug('FBX: authorized order successfully');

        $paymentTransaction
            ->setActive(true)
            ->setSuccessful(true);
        $this->logger->debug('FBX: finished payment transction');
    }

    private function finishSuccessfulTransaction(PaymentTransaction $paymentTransaction)
    {
        $paymentTransaction->setActive(false)->setSuccessful(true);
    }

    private function getAmountCents($amount)
    {
        return (string) ((float) $amount * 100);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function isApplicable(PaymentContextInterface $context)
    {
        return $this->isCurrencySupported($context->getCurrency()) &&
        $this->areAddressesSupported($context->getBillingAddress(), $context->getShippingAddress());
    }

    /**
     * {@inheritDoc}
     */
    public function supports($actionName)
    {
        return in_array(
            $actionName, [self::AUTHORIZE, self::CAPTURE, self::PURCHASE], true
        );
    }

    /**
     * @param AddressInterface $billingAddress
     * @param AddressInterface $shippingAddress
     *
     * @return boolean
     */
    private function areAddressesSupported($billingAddress, $shippingAddress)
    {
        $billingCountry = $billingAddress->getCountryIso2();
        $shippingCountry = $shippingAddress->getCountryIso2();
        return in_array($billingCountry, $this->supportedContryCodes) && in_array($shippingCountry, $this->supportedContryCodes);
    }

    /**
     * @param string $currency
     *
     * @return boolean
     */
    private function isCurrencySupported($currency)
    {
        return in_array($currency, $this->supportedCurrencyCodes);
    }
}
