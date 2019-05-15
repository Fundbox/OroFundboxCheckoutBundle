<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\View;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

class FundboxCheckoutView implements PaymentMethodViewInterface
{
    /**
     * @var FundboxCheckoutConfigInterface
     */
    protected $config;

    /**
     * @var FundboxCheckoutTransport
     */
    protected $fundboxCheckoutTransport;

    /**
     * @param FundboxCheckoutConfigInterface $config
     * @param FundboxCheckoutTransport $fundboxCheckoutTransport
     */
    public function __construct(
        FundboxCheckoutConfigInterface $config,
        FundboxCheckoutTransport $fundboxCheckoutTransport
    ) {
        $this->config = $config;
        $this->fundboxCheckoutTransport = $fundboxCheckoutTransport;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(PaymentContextInterface $context)
    {
        $merchantDetails = $this->fundboxCheckoutTransport->getMerchantDetails($this->config->getPublicKey());
        return [
            "gracePeriod" => $merchantDetails["grace_period"],
            "publicKey" => $this->config->getPublicKey(),
            "envUrl" => $this->config->getFbxBaseUrl(),
            "transactionType" => strtolower($this->config->getPaymentAction()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getBlock()
    {
        return '_payment_methods_fundbox_checkout_widget';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->config->getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getShortLabel()
    {
        return $this->config->getShortLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getAdminLabel()
    {
        return $this->config->getAdminLabel();
    }

    /** {@inheritDoc} */
    public function getPaymentMethodIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }
}
