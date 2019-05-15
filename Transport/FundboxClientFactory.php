<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Transport;

use Fundbox\Bundle\FundboxCheckoutBundle\Method\Config\FundboxCheckoutConfigInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientFactoryInterface;

class FundboxClientFactory
{
    /** @var RestClientFactoryInterface */
    private $restClientFactory;

    /**
     * @param RestClientFactoryInterface $restClientFactory
     */
    public function __construct(RestClientFactoryInterface $restClientFactory) {
        $this->restClientFactory = $restClientFactory;
    }

    /**
     * @param FundboxCheckoutConfigInterface $config
     *
     * @return FundboxClient
     */
    public function create(FundboxCheckoutConfigInterface $config)
    {
        $restClient = $this->restClientFactory->createRestClient(
            $config->getFbxBaseUrl() . '/api/v1/',
            ['auth' => [$config->getPublicKey(), $config->getPrivateKey()]]
        );
        return new FundboxClient($restClient);
    }
}
