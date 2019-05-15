<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Transport;

use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientInterface;

class FundboxClient
{
    /**
     * @var RestClientInterface
     */
    protected $restClient;

    /**
     * @param RestClientInterface $restClient
     */
    public function __construct(RestClientInterface $restClient) {
        $this->restClient = $restClient;
    }

    public function get($resource, array $params = [], array $headers = [], array $options = [])
    {
        return $this->restClient->get($resource, $params, $headers, $options);
    }

    public function getJSON($resource, array $params = [], array $headers = [], array $options = [])
    {
        return $this->restClient->getJSON($resource, $params, $headers, $options);
    }

    public function post($resource, $data, array $headers = [], array $options = [])
    {
        return $this->restClient->post($resource, $data, $headers, $options);
    }
}
