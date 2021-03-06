<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigProviderInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Method\FundboxCheckoutMethodFactoryInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransportFactory;
use Oro\Bundle\PaymentBundle\Method\Provider\AbstractPaymentMethodProvider;
use Oro\Bundle\PricingBundle\SubtotalProcessor\Provider\AbstractSubtotalProvider;
use Psr\Log\LoggerInterface;

class FundboxCheckoutMethodProvider extends AbstractPaymentMethodProvider
{
    /**
     * @var FundboxCheckoutMethodFactoryInterface
     */
    protected $factory;

    /** @var FundboxCheckoutTransportFactory */
    private $fundboxCheckoutTransportFactory;

    /**
     * @var FundboxCheckoutConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var AbstractSubtotalProvider
     */
    protected $subtotalProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FundboxCheckoutMethodFactoryInterface $factory
     * @param FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory
     * @param FundboxCheckoutConfigProviderInterface $configProvider
     * @param AbstractSubtotalProvider $subtotalProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        FundboxCheckoutMethodFactoryInterface $factory,
        FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory,
        FundboxCheckoutConfigProviderInterface $configProvider,
        AbstractSubtotalProvider $subtotalProvider,
        LoggerInterface $logger
    ) {
        $this->factory = $factory;
        $this->fundboxCheckoutTransportFactory = $fundboxCheckoutTransportFactory;
        $this->configProvider = $configProvider;
        $this->subtotalProvider = $subtotalProvider;
        $this->logger = $logger;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function collectMethods()
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addFundboxCheckoutMethod($config);
        }
    }

    /**
     * @param FundboxCheckoutConfigInterface $config
     */
    protected function addFundboxCheckoutMethod(FundboxCheckoutConfigInterface $config)
    {
        $this->addMethod(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create(
                $config,
                $this->fundboxCheckoutTransportFactory->create($config),
                $this->subtotalProvider,
                $this->logger
            )
        );
    }
}
