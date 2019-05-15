<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Entity\Repository;

use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;
use Doctrine\ORM\EntityRepository;

class FundboxCheckoutSettingsRepository extends EntityRepository
{
    /**
     * @return FundboxCheckoutSettings[]
     */
    public function getEnabledSettings()
    {
        return $this->createQueryBuilder('settings')
            ->innerJoin('settings.channel', 'channel')
            ->andWhere('channel.enabled = true')
            ->getQuery()
            ->getResult();
    }
}