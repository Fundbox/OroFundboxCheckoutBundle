<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\View;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigProviderInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransportFactory;
use Oro\Bundle\PaymentBundle\Method\View\AbstractPaymentMethodViewProvider;

class FundboxCheckoutViewProvider extends AbstractPaymentMethodViewProvider
{
    /** @var FundboxCheckoutViewFactoryInterface */
    private $factory;

    /** @var FundboxCheckoutConfigProviderInterface */
    private $configProvider;

    /** @var FundboxCheckoutTransportFactory */
    private $fundboxCheckoutTransportFactory;

    /**
     * @param FundboxCheckoutViewFactoryInterface $factory
     * @param FundboxCheckoutConfigProviderInterface $configProvider
     * @param FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory
     */
    public function __construct(
        FundboxCheckoutViewFactoryInterface $factory,
        FundboxCheckoutConfigProviderInterface $configProvider,
        FundboxCheckoutTransportFactory $fundboxCheckoutTransportFactory
    ) {
        $this->factory = $factory;
        $this->configProvider = $configProvider;
        $this->fundboxCheckoutTransportFactory = $fundboxCheckoutTransportFactory;
        
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
            $this->factory->create($config, $this->fundboxCheckoutTransportFactory->create($config))
        );
    }
}