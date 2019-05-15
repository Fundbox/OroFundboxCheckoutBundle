<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Psr\Log\LoggerInterface;

interface FundboxCheckoutMethodFactoryInterface
{
    /**
     * @param FundboxCheckoutConfigInterface $config
     * @param FundboxCheckoutTransport $fundboxCheckoutTransport
     * @param LoggerInterface $logger
     *
     * @return PaymentMethodInterface
     */
    public function create(
        FundboxCheckoutConfigInterface $config,
        FundboxCheckoutTransport $fundboxCheckoutTransport,
        LoggerInterface $logger
    );
}
