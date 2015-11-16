<?php

use Snorlax\Resource\Resource;

class PokemonResource extends Resource
{
    const BASE_PATH = 'pokemons';

    public function getBaseUri()
    {
        return static::BASE_PATH;
    }
}
