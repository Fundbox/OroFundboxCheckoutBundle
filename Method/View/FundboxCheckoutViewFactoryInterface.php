<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\View;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Fundbox\Bundle\FundboxCheckoutBundle\Transport\FundboxCheckoutTransport;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

interface FundboxCheckoutViewFactoryInterface
{
    /**
     * @param FundboxCheckoutConfigInterface $config
     * @param FundboxCheckoutTransport $fundboxCheckoutTransport
     * @return PaymentMethodViewInterface
     */
    public function create(FundboxCheckoutConfigInterface $config, FundboxCheckoutTransport $fundboxCheckoutTransport);
}
