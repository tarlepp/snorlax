<?php

use Mockery as m;

class RestClientTest extends TestCase
{
    public function testResources()
    {
        $client = $this->getRestClient();

        $this->assertInstanceOf('PokemonResource', $client->pokemons);
    }

    /**
     * @expectedException \Snorlax\Exception\ResourceNotImplemented
     * @expectedExceptionMessage Resource "digimons" is not implemented
     */
    public function testResourceDoesNotImplemented()
    {
        $this->getRestClient()->digimons;
    }

    public function testCustomClientInstance()
    {
        $custom_client = m::mock('GuzzleHttp\ClientInterface');

        $client = $this->getRestClient([
            'custom' => $custom_client,
            'params' => []
        ]);

        $this->assertSame($custom_client, $client->getOriginalClient());
    }

    public function testCustomClientClosure()
    {
        $custom_client = m::mock('GuzzleHttp\ClientInterface');
        $client = $this->getRestClient([
            'custom' => function(array $params) use ($custom_client){
                return $custom_client;
            },
            'params' => []
        ]);

        $this->assertSame($custom_client, $client->getOriginalClient());
    }
}
