<?php

namespace Snorlax\Resource;

use GuzzleHttp\ClientInterface;

abstract class Resource
{
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    abstract public function getBaseUri();
}
