<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\View;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigProviderInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransportFactory;
use Oro\Bundle\PaymentBundle\Method\View\AbstractPaymentMethodViewProvider;
use Psr\Log\LoggerInterface;

class FundboxCheckoutViewProvider extends AbstractPaymentMethodViewProvider
{
    /** @var FundboxCheckoutViewFactoryInterface */
    private $factory;

    /** @var FundboxCheckoutConfigProviderInterface */
    private $configProvider;

    /** @var FundboxCheckoutTransportFactory */
    private $fundboxCheckoutTransportFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FundboxCheckoutViewFactoryInterface $factory
     * @param FundboxCheckoutConfigProviderInterface $configProvider
     * @param FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        FundboxCheckoutViewFactoryInterface $factory,
        FundboxCheckoutConfigProviderInterface $configProvider,
        FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory,
        LoggerInterface $logger
    ) {
        $this->factory = $factory;
        $this->configProvider = $configProvider;
        $this->fundboxCheckoutTransportFactory = $fundboxCheckoutTransportFactory;
        $this->logger = $logger;
        
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function buildViews()
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addFundboxCheckoutView($config);
        }
    }

    /**
     * @param FundboxCheckoutConfigInterface $config
     */
    protected function addFundboxCheckoutView(FundboxCheckoutConfigInterface $config)
    {
        $this->addView(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create(
                $config,
                $this->fundboxCheckoutTransportFactory->create($config),
                $this->logger
            )
        );
    }
}