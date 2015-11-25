<?php

use Mockery as m;
use GuzzleHttp\Psr7\Response;

class ResourceTest extends TestCase
{
    public function testAllMethod()
    {
        $json = json_encode([
            'pokemons' => []
        ]);
        $mock = new Response(200, [], $json);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle
            ->shouldReceive('request')
            ->with('GET', 'pokemons/', [])
            ->once()
            ->andReturn($mock);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->all();

        $this->assertEquals([], $response->pokemons);
        $this->assertEquals($mock, $client->pokemons->getLastResponse());
    }

    public function testGetMethod()
    {
        $json = json_encode([
            'pokemon' => [
                'id' => 143,
                'name' => 'Snorlax'
            ]
        ]);
        $mock = new Response(200, [], $json);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle
            ->shouldReceive('request')
            ->with('GET', 'pokemons/143', [])
            ->once()
            ->andReturn($mock);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->get(143);

        $this->assertEquals((object) [
            'id' => 143,
            'name' => 'Snorlax'
        ], $response->pokemon);
        $this->assertEquals($mock, $client->pokemons->getLastResponse());
    }

    public function testPostMethod()
    {
        $mock = new Response(201);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle
            ->shouldReceive('request')
            ->with('POST', 'pokemons/', ['body' => ['pokemon_id' => 143]])
            ->once()
            ->andReturn($mock);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->capture([
            'body' => ['pokemon_id' => 143]
        ]);

        $this->assertEquals($mock, $client->pokemons->getLastResponse());
    }

    public function testPatchMethod()
    {
        $mock = new Response(204);

        $guzzle = m::mock('GuzzleHttp\ClientInterface');
        $guzzle
            ->shouldReceive('request')
            ->with('PATCH', 'pokemons/143/144/rest', [])
            ->once()
            ->andReturn($mock);

        $client = $this->getRestClient([
            'custom' => $guzzle
        ]);

        $response = $client->pokemons->attack(143, 144, 'rest');

        $this->assertEquals($mock, $client->pokemons->getLastResponse());
    }
}
