<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\View;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;
use Psr\Log\LoggerInterface;

class FundboxCheckoutViewFactory implements FundboxCheckoutViewFactoryInterface
{
    /**
     * @param FundboxCheckoutConfigInterface $config
     * @param FundboxCheckoutTransport $fundboxCheckoutTransport
     * @param LoggerInterface $logger
     * @return PaymentMethodViewInterface
     */
    public function create(
        FundboxCheckoutConfigInterface $config,
        FundboxCheckoutTransport $fundboxCheckoutTransport,
        LoggerInterface $logger
    ) {
        return new FundboxCheckoutView($config, $fundboxCheckoutTransport, $logger);
    }
}
