<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\Config;

use Doctrine\Common\Collections\Collection;
use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\SecurityBundle\Encoder\DefaultCrypter;
use Oro\Bundle\SecurityBundle\Encoder\SymmetricCrypterInterface;


class FundboxCheckoutConfigFactory implements FundboxCheckoutConfigFactoryInterface
{
    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $identifierGenerator;

    /**
     * @var DefaultCrypter
     */
    private $encoder;

    /**
     * @param LocalizationHelper $localizationHelper
     * @param IntegrationIdentifierGeneratorInterface $identifierGenerator
     * @param SymmetricCrypterInterface $encoder
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        SymmetricCrypterInterface $encoder
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->identifierGenerator = $identifierGenerator;
        $this->encoder= $encoder;
    }

    /**
     * {@inheritDoc}
     */
    public function create(FundboxCheckoutSettings $settings)
    {
        $channel = $settings->getChannel();

        return new FundboxCheckoutConfig(
            $this->getLocalizedValue($settings->getLabels()),
            $this->getLocalizedValue($settings->getShortLabels()),
            $channel->getName(),
            $this->identifierGenerator->generateIdentifier($channel),
            $settings->getEnvironment(),
            $this->getDecryptedValue($settings->getProductionPublicKey()),
            $this->getDecryptedValue($settings->getProductionPrivateKey()),
            $this->getDecryptedValue($settings->getTestPublicKey()),
            $this->getDecryptedValue($settings->getTestPrivateKey()),
            $settings->getLogEnabled(),
            $settings->getPaymentAction(),
            $settings->getMinimumOrder(),
            $settings->getMaximumOrder()
        );
    }

    /**
     * @param Collection $values
     *
     * @return string
     */
    private function getLocalizedValue(Collection $values)
    {
        return (string)$this->localizationHelper->getLocalizedValue($values);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function getDecryptedValue($value)
    {
        return (string)$this->encoder->decryptData($value);
    }
}
