<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Transport;

class FundboxCheckoutTransport
{
    /**
     * @var FundboxClient
     */
    protected $client;

    /**
     * @param FundboxClient $fundboxClient
     */
    public function __construct(FundboxClient $fundboxClient)
    {
        $this->client = $fundboxClient;
    }

    public function getMerchantDetails($publicKey)
    {
        return $this->client->getJSON('merchants/' . $publicKey);
    }

    public function applyOrder($transactionToken, $amountCents)
    {
        return $this->client->post(
            'orders/' . $transactionToken . '/apply',
            ['amount_cents' => $amountCents]
        );
    }

    public function authorizeOrder($transactionToken, $amountCents)
    {
        return $this->client->post(
            'orders/' . $transactionToken . '/authorize',
            ['amount_cents' => $amountCents]
        );
    }
}
