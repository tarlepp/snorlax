<?php

use Snorlax\Resource\Resource;
use Snorlax\Common\Get;

class PokemonResource extends Resource
{
    use Get;

    const BASE_PATH = 'pokemons';

    public function getBaseUri()
    {
        return static::BASE_PATH;
    }
}
