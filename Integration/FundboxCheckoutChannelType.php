<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

class FundboxCheckoutChannelType implements ChannelInterface, IconAwareIntegrationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'fundbox.checkout.channel_type.label';
    }

    /**
     * {@inheritDoc}
     */
    public function getIcon()
    {
        return 'bundles/fundboxcheckout/img/fbx-logo.png';
    }
}