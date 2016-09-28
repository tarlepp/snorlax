<?php

namespace Snorlax;

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
    protected $last_response;

    /**
     * Initializes the client
     * @param GuzzleHttp\ClientInterface
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Calls the method contained in the actions of this resource
     * @param string $method
     * @param array $args
     * @return \StdClass The JSON decoded response
     */
    public function __call($method, $args)
    {
        $action = $this->getActions()[$method];
        $uri = $this->getBaseUri() . $this->getPath($action, $args);
        $params = $this->getParams($args);

        $this->last_response = $this->client->request($action['method'], $uri, $params);
        
        $response = json_decode($this->last_response->getBody());
        
        return $this->after($method, $response);

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
     * Returns the last_response of the last executed request
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->last_response;
    }
    
    /**
     * Method called after just before the reponse is returned
     * @param string $method 
     * @param string $response 
     * @return string
     */
    protected function after($method, $response){
        return $response;
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
