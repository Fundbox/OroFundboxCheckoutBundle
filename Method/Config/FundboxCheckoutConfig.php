<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Method\Config;

class FundboxCheckoutConfig implements FundboxCheckoutConfigInterface
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $shortLabel;

    /**
     * @var string
     */
    protected $adminLabel;

    /**
     * @var string
     */
    protected $paymentMethodIdentifier;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $productionPublicKey;

    /**
     * @var string
     */
    protected $productionPrivateKey;

    /**
     * @var string
     */
    protected $testPublicKey;

    /**
     * @var string
     */
    protected $testPrivateKey;

    /**
     * @var boolean
     */
    protected $logEnabled = false;

    /**
     * @var string
     */
    protected $paymentAction;

    /**
     * @var integer
     */
    protected $minimumOrder;

    /**
     * @var integer
     */
    protected $maximumOrder;

    /**
     * @param string $label
     * @param string $shortLabel
     * @param string $adminLabel
     * @param string $paymentMethodIdentifier
     * @param string $environment
     * @param string $productionPublicKey
     * @param string $productionPrivateKey
     * @param string $testPublicKey
     * @param string $testPrivateKey
     * @param boolean $logEnabled
     * @param string $paymentAction
     * @param integer $minimumOrder
     * @param integer $maximumOrder
     */
    public function __construct(
        $label,
        $shortLabel,
        $adminLabel,
        $paymentMethodIdentifier,
        $environment,
        $productionPublicKey,
        $productionPrivateKey,
        $testPublicKey,
        $testPrivateKey,
        $logEnabled,
        $paymentAction,
        $minimumOrder,
        $maximumOrder
    ) {
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->adminLabel = $adminLabel;
        $this->paymentMethodIdentifier = $paymentMethodIdentifier;
        $this->environment = $environment;
        $this->productionPublicKey = $productionPublicKey;
        $this->productionPrivateKey = $productionPrivateKey;
        $this->testPublicKey = $testPublicKey;
        $this->testPrivateKey = $testPrivateKey;
        $this->logEnabled = $logEnabled;
        $this->paymentAction = $paymentAction;
        $this->minimumOrder = $minimumOrder;
        $this->maximumOrder = $maximumOrder;
    }

    /**
     * @return boolean
     */
    public function isProduction()
    {
        return $this->environment == "production";
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getShortLabel()
    {
        return $this->shortLabel;
    }

    /**
     * @return string
     */
    public function getAdminLabel()
    {
        return $this->adminLabel;
    }

    /**
     * @return string
     */
    public function getPaymentMethodIdentifier()
    {
        return $this->paymentMethodIdentifier;
    }

     /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->isProduction() ? $this->productionPublicKey : $this->testPublicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->isProduction() ? $this->productionPrivateKey : $this->testPrivateKey;
    }

    /**
     * @return bool
     */
    public function getLogEnabled()
    {
        return $this->logEnabled;
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * @return integer
     */
    public function getMinimumOrder()
    {
        return $this->minimumOrder;
    }

    /**
     * @return integer
     */
    public function getMaximumOrder()
    {
        return $this->maximumOrder;
    }

    /**
     * @return string
     */
    public function getFbxBaseUrl()
    {
        return $this->isProduction() ? "https://checkout.fundboxpay.com" : "https://checkout-integration.fundboxpay.com";
    }
}
