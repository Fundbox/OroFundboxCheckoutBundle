<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\Config;

use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;
use Fundbox\Bundle\FundboxCheckoutBundle\Entity\Repository\FundboxCheckoutSettingsRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class FundboxCheckoutConfigProvider implements FundboxCheckoutConfigProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var FundboxCheckoutConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * @var FundboxCheckoutConfigInterface[]
     */
    protected $configs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param FundboxCheckoutConfigFactory $configFactory
     */
    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        FundboxCheckoutConfigFactory $configFactory
    ) {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentConfigs()
    {
        $configs = [];

        $settings = $this->getEnabledIntegrationSettings();

        foreach ($settings as $setting) {
            $config = $this->configFactory->create($setting);

            $configs[$config->getPaymentMethodIdentifier()] = $config;
        }

        return $configs;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentConfig($identifier)
    {
        $paymentConfigs = $this->getPaymentConfigs();

        if ([] === $paymentConfigs || false === array_key_exists($identifier, $paymentConfigs)) {
            return null;
        }

        return $paymentConfigs[$identifier];
    }

    /**
     * {@inheritDoc}
     */
    public function hasPaymentConfig($identifier)
    {
        return null !== $this->getPaymentConfig($identifier);
    }

    /**
     * @return FundboxCheckoutSettings[]
     */
    protected function getEnabledIntegrationSettings()
    {
        try {
            /** @var FundboxCheckoutSettingsRepository $repository */
            $repository = $this->doctrine
                ->getManagerForClass(FundboxCheckoutSettings::class)
                ->getRepository(FundboxCheckoutSettings::class);

            return $repository->getEnabledSettings();
        } catch (\UnexpectedValueException $e) {
            $this->logger->critical($e->getMessage());

            return [];
        }
    }
}