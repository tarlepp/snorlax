<?php

use Snorlax\Resource\Resource;

class PokemonResource extends Resource
{
    public function getBaseUri()
    {
        return 'pokemons';
    }

    public function getActions()
    {
        return [
            'all' => [
                'method' => 'GET',
                'path' => '/'
            ],
            'get' => [
                'method' => 'GET',
                'path' => '/{0}'
            ],
            'capture' => [
                'method' => 'POST',
                'path' => '/'
            ],
            'attack' => [
                'method' => 'PATCH',
                'path' => '/{0}/{1}/{2}'
            ]
        ];
    }
}
