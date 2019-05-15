<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Integration;

use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;
use Fundbox\Bundle\FundboxCheckoutBundle\Form\Type\FundboxCheckoutSettingsType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

class FundboxCheckoutTransport implements TransportInterface
{   
    /**
     * {@inheritDoc}
     */
    public function init(Transport $transportEntity)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'fundbox.checkout.settings.transport.label';
    }

    /**
     * {@inheritDoc}
     */
    public function getSettingsFormType()
    {
        return FundboxCheckoutSettingsType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getSettingsEntityFQCN()
    {
        return FundboxCheckoutSettings::class;
    }
}
