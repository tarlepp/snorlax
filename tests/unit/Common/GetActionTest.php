<?php

use Mockery as m;
use GuzzleHttp\Psr7\Response;

class GetActionTest extends TestCase
{
    public function testAllFromResource()
    {
        $json = json_encode([
            'pokemons' => []
        ]);
        $response = new Response(200, [], $json);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle->shouldReceive('get')->with('pokemons', [])->once()->andReturn($response);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->all();

        $this->assertEquals([], $response->pokemons);
    }

    public function testGetOne()
    {
        $json = json_encode([
            'pokemon' => [
                'id' => 143,
                'name' => 'Snorlax'
            ]
        ]);
        $response = new Response(200, [], $json);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle->shouldReceive('get')->with('pokemons/143', [])->once()->andReturn($response);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->get(143);

        $this->assertEquals((object) [
            'id' => 143,
            'name' => 'Snorlax'
        ], $response->pokemon);
    }
}
