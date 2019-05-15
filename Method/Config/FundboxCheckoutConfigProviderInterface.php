<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\Config;

interface FundboxCheckoutConfigProviderInterface
{
    /**
     * @return FundboxCheckoutConfigInterface[]
     */
    public function getPaymentConfigs();

    /**
     * @param string $identifier
     * @return FundboxCheckoutConfigInterface|null
     */
    public function getPaymentConfig($identifier);

    /**
     * @param string $identifier
     * @return bool
     */
    public function hasPaymentConfig($identifier);
}