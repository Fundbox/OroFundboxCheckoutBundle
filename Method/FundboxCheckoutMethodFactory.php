<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Psr\Log\LoggerInterface;

class FundboxCheckoutMethodFactory implements FundboxCheckoutMethodFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(
        FundboxCheckoutConfigInterface $config,
        FundboxCheckoutTransport $fundboxCheckoutTransport,
        LoggerInterface $logger
    ) {
        return new FundboxCheckoutMethod($config, $fundboxCheckoutTransport, $logger);
    }
}
