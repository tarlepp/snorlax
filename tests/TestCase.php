<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    public function getRestClient(array $client_config=[])
    {
        $resources = [
            'pokemons' => PokemonResource::class
        ];

        return new Snorlax\RestClient([
            'client' => $client_config,
            'resources' => $resources
        ]);
    }
}
