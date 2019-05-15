<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Transport;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;

class FundboxCheckoutTransportFactory
{

    /** @var FundboxClientFactory */
    private $fundboxClientFactory;

    /**
     * @param FundboxClientFactory $fundboxClientFactory
     */
    public function __construct(FundboxClientFactory $fundboxClientFactory) {
        $this->fundboxClientFactory = $fundboxClientFactory;
    }

    /**
     * @param FundboxCheckoutConfigInterface $config
     *
     * @return FundboxCheckoutTransport
     */
    public function create(FundboxCheckoutConfigInterface $config)
    {
        $fundboxClient = $this->fundboxClientFactory->create($config);
        return new FundboxCheckoutTransport($fundboxClient);
    }
}
