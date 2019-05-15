<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\Config;

use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;

interface FundboxCheckoutConfigFactoryInterface
{
    /**
     * @param FundboxCheckoutSettings $settings
     * @return FundboxCheckoutConfigInterface
     */
    public function create(FundboxCheckoutSettings $settings);
}