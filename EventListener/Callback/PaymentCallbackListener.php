<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\EventListener\Callback;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigProviderInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Method\FundboxCheckoutMethod;
use Oro\Bundle\PaymentBundle\Event\AbstractCallbackEvent;
use Oro\Bundle\PaymentBundle\Method\Provider\PaymentMethodProviderInterface;
use Psr\Log\LoggerAwareTrait;

class PaymentCallbackListener
{
    use LoggerAwareTrait;

    /**
     * @var PaymentMethodProviderInterface
     */
    protected $paymentMethodProvider;

    /**
     * @var FundboxCheckoutConfigProviderInterface
     */
    private $configProvider;

    /**
     * @param PaymentMethodProviderInterface $paymentMethodProvider
     * @param FundboxCheckoutConfigProviderInterface $configProvider
     */
    public function __construct(
        PaymentMethodProviderInterface $paymentMethodProvider,
        FundboxCheckoutConfigProviderInterface $configProvider
    ) {
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param AbstractCallbackEvent $event
     */
    public function onReturn(AbstractCallbackEvent $event)
    {
        $paymentTransaction = $event->getPaymentTransaction();

        if (!$paymentTransaction) {
            return;
        }

        $paymentMethodId = $paymentTransaction->getPaymentMethod();

        if (false === $this->paymentMethodProvider->hasPaymentMethod($paymentMethodId)) {
            return;
        }

        $eventData = $event->getData();

        if (!array_key_exists(FundboxCheckoutMethod::TRANSACTION_TOKEN_KEY, $eventData)) {
            return;
        }

        $this->logger->debug('FBX: callback returned with ransaction token');
        $paymentTransaction->setResponse($eventData);

        try {
            $paymentMethod = $this->paymentMethodProvider->getPaymentMethod($paymentMethodId);

            $action = $this->getPaymentAction($paymentMethodId);
            $this->logger->debug(
                sprintf('FBX: callback trigger "%s"', $action)
            );
            $paymentMethod->execute($action, $paymentTransaction);

            $event->markSuccessful();
        } catch (\InvalidArgumentException $e) {
            if ($this->logger) {
                // Do not expose sensitive data in context.
                $this->logger->error($e->getMessage(), []);
            }
        }
    }

    private function getPaymentAction($paymentMethodId)
    {
        $config = $this->configProvider->getPaymentConfig($paymentMethodId);
        $action = $config->getPaymentAction();
        return strtolower($action);
    }
}
