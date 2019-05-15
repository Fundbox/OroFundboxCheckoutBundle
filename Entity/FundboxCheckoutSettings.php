<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Represents entity for Fundbox Checkout payment method integration settings.
 *
 * @ORM\Entity(
 *     repositoryClass="Fundbox\Bundle\FundboxCheckoutBundle\Entity\Repository\FundboxCheckoutSettingsRepository"
 * )
 */
class FundboxCheckoutSettings extends Transport
{

    const ENVIRONMENT = 'environment';
    const PRODUCTION_PUBLIC_API_KEY = 'production_public_key';
    const PRODUCTION_PRIVATE_API_KEY = 'production_private_key';
    const TEST_PUBLIC_API_KEY = 'test_public_key';
    const TEST_PRIVATE_API_KEY = 'test_private_key';
    const LOG_ENABLED = 'log_enabled';
    const PAYMENT_ACTION = 'payment_action';
    const LABELS = 'labels';
    const SHORT_LABELS = 'short_labels';
    const MINIMUM_ORDER = 'minimum_order';
    const MAXIMUM_ORDER = 'maximum_order';

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_environment", type="string", length=50, nullable=false)
     */
    protected $environment;

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_production_public_key", type="string", length=255, nullable=false)
     */
    protected $productionPublicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_production_private_key", type="string", length=255, nullable=false)
     */
    protected $productionPrivateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_test_public_key", type="string", length=255, nullable=false)
     */
    protected $testPublicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_test_private_key", type="string", length=255, nullable=false)
     */
    protected $testPrivateKey;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fbx_log_enabled", type="boolean", options={"default"=false})
     */
    protected $logEnabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="fbx_payment_action", type="string", length=255, nullable=false)
     */
    protected $paymentAction;

    /**
     * @var integer
     *
     * @ORM\Column(name="fbx_minimum_order", type="integer", nullable=false, options={"default"=10})
     */
    protected $minimumOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="fbx_maximum_order", type="integer", nullable=false, options={"default"=100000})
     */
    protected $maximumOrder;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="fbx_trans_label",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $labels;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="fbx_short_label",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $shortLabels;

    /**
     * @var ParameterBag
     */
    private $settings;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->shortLabels = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getProductionPublicKey()
    {
        return $this->productionPublicKey;
    }

    /**
     * @param string $productionPublicKey
     */
    public function setProductionPublicKey($productionPublicKey)
    {
        $this->productionPublicKey = $productionPublicKey;
    }

    /**
     * @return string
     */
    public function getProductionPrivateKey()
    {
        return $this->productionPrivateKey;
    }

    /**
     * @param string $productionPrivateKey
     */
    public function setProductionPrivateKey($productionPrivateKey)
    {
        $this->productionPrivateKey = $productionPrivateKey;
    }

    /**
     * @return string
     */
    public function getTestPublicKey()
    {
        return $this->testPublicKey;
    }

    /**
     * @param string $testPublicKey
     */
    public function setTestPublicKey($testPublicKey)
    {
        $this->testPublicKey = $testPublicKey;
    }

    /**
     * @return string
     */
    public function getTestPrivateKey()
    {
        return $this->testPrivateKey;
    }

    /**
     * @param string $testPrivateKey
     */
    public function setTestPrivateKey($testPrivateKey)
    {
        $this->testPrivateKey = $testPrivateKey;
    }

    /**
     * @return bool
     */
    public function getLogEnabled()
    {
        return $this->logEnabled;
    }

    /**
     * @param bool $logEnabled
     */
    public function setLogEnabled($logEnabled)
    {
        $this->logEnabled = $logEnabled;
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * @param string $paymentAction
     */
    public function setPaymentAction($paymentAction)
    {
        $this->paymentAction = $paymentAction;
    }

    /**
     * @return int
     */
    public function getMinimumOrder()
    {
        return $this->minimumOrder;
    }

    /**
     * @param int $minimumOrder
     */
    public function setMinimumOrder($minimumOrder)
    {
        $this->minimumOrder = $minimumOrder;
    }

    /**
     * @return int
     */
    public function getMaximumOrder()
    {
        return $this->maximumOrder;
    }

    /**
     * @param int $maximumOrder
     */
    public function setMaximumOrder($maximumOrder)
    {
        $this->maximumOrder = $maximumOrder;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function addLabel(LocalizedFallbackValue $label)
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function removeLabel(LocalizedFallbackValue $label)
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getShortLabels()
    {
        return $this->shortLabels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function addShortLabel(LocalizedFallbackValue $label)
    {
        if (!$this->shortLabels->contains($label)) {
            $this->shortLabels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function removeShortLabel(LocalizedFallbackValue $label)
    {
        if ($this->shortLabels->contains($label)) {
            $this->shortLabels->removeElement($label);
        }

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag(
                [
                    self::LABELS => $this->getLabels(),
                    self::SHORT_LABELS => $this->getShortLabels(),
                    self::ENVIRONMENT => $this->getEnvironment(),
                    self::PRODUCTION_PUBLIC_API_KEY => $this->getProductionPublicKey(),
                    self::PRODUCTION_PRIVATE_API_KEY => $this->getProductionPrivateKey(),
                    self::TEST_PUBLIC_API_KEY => $this->getTestPublicKey(),
                    self::TEST_PRIVATE_API_KEY => $this->getTestPrivateKey(),
                    self::LOG_ENABLED => $this->getLogEnabled(),
                    self::PAYMENT_ACTION => $this->getPaymentAction(),
                    self::MINIMUM_ORDER => $this->getMinimumOrder(),
                    self::MAXIMUM_ORDER => $this->getMaximumOrder()
                ]
            );
        }

        return $this->settings;
    }
}
