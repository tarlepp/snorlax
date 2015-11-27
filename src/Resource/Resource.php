<?php

namespace Snorlax\Resource;

use GuzzleHttp\ClientInterface;

/**
 * The mother class of all resources. Contains methods to make dynamic requests
 * defined by the Resource::getActions() method
 */
abstract class Resource
{
    /**
     * @var GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var mixed
     */
    protected $response;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function __call($method, $args)
    {
        $action = $this->getActions()[$method];
        $uri = $this->getBaseUri() . $this->getPath($action, $args);
        $params = $this->getParams($args);

        $this->response = $this->client->request($action['method'], $uri, $params);

        return json_decode($this->response->getBody());
    }

    /**
     * Returns the URI path for the request
     * @param array $action
     * @param array $args
     * @param string
     */
    private function getPath(array $action, array $args)
    {
        return preg_match_all('/{(\d+)}/', $action['path'], $matches) ?
            str_replace($matches[0], $args, $action['path']) :
            $action['path'];
    }

    /**
     * Extracts the params from the arguments passed
     * @param array
     * @param array
     */
    private function getParams(array $args)
    {
        if (count($args) == 0) {
            return [];
        }

        $params = array_slice($args, -1)[0];

        return is_array($params) ? $params : [];
    }

    /**
     * Returns the response of the last executed request
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    /**
     * Returns the base URI for every request
     * @return string
     */
    abstract public function getBaseUri();

    /**
     * Returns the actions available for this resource
     * @return array
     */
    abstract public function getActions();
}
