# Snorlax

[![Build Status](https://travis-ci.org/ezdeliveryco/snorlax.svg?branch=master)](https://travis-ci.org/ezdeliveryco/snorlax)
[![StyleCI](https://styleci.io/repos/46307948/shield?branch=master)](https://styleci.io/repos/69507965)
[![codecov](https://codecov.io/gh/ezdeliveryco/snorlax/branch/master/graph/badge.svg)](https://codecov.io/gh/ezdeliveryco/snorlax)

A light-weight RESTful client built on top of [Guzzle](http://docs.guzzlephp.org/en/latest/) that gives you full control of your API's resources. Its based on method definitions and parameters for your URLs. See the usage below.

# Basic Usage

```php
<?php

use Snorlax\Resource;
use Snorlax\RestClient;

class PokemonResource extends Resource
{
    public function getBaseUri()
    {
        // You don't want a raw value like this, use an environment variable :)
        return 'http://localhost/api/pokemons';
    }

    public function getActions()
    {
        return [
            'all' => [
                'method' => 'GET',
                'path' => '/',
            ],
            'get' => [
                'method' => 'GET',
                'path' => '/{0}.json',
            ],
            'create' => [
                'method' => 'POST',
                'path' => '/',
            ],
        ];
    }
}

$client = new RestClient([
    'resources' => [
        'pokemons' => PokemonResource::class,
    ],
]);

// GET http://localhost/api/pokemons?sort=id:asc
$response = $client->pokemons->all([
    'query' => [
        'sort' => 'id:asc',
    ],
]);

// GET http://localhost/api/pokemons/143.json?fields=id,name
$response = $client->pokemons->get(143, [
    'query' => [
        'fields' => 'id,name',
    ],
]);

// POST http://localhost/api/pokemons
$response = $client->pokemons->create([
    'body' => [
        'name' => 'Bulbasaur',
    ],
]);
```

As you can see, each action on your resource is defined an array with two keys, `method`, defining the HTTP method for the request and `path`, which defines the path from the base URI returned by the `getBaseUri` method. You can mock URLs using environment variables as you wish.

`Snorlax` assume your API returns JSON, so it already returns an `StdClass` object with the response, decoded by `json_decode`. If you want to get the raw object returned by `Guzzle`, use `$client->resource->getLastResponse()`.

# Amending the response

As noted above, `Snorlax` returns an `StdClass` object, however Resources may overwrite the `->parse()` method to manipulate the returned response. This is useful when an API returns a nested set of data such as `{'pokemon': {'name':'Mew'}}` and you only want the actual data (in this case `pokemon`). In this example we could use

```php
public function parse($method, $response)
{
    return $response->pokemon;
}
```

This would return the actual `pokemon` object. Another scario is that you may want to return a Laravel Collection (`Illuminate\Support\Collection`) of objects, you could simply do

```php
public function parse($method, $response)
{
    return collect($response->pokemon);
}
```

The `$method` argument is the name of the method which was called to perform the request, such as 'all', or 'get'. This is useful to manipulate different response, such as

```php
public function parse($method, $response)
{
    switch ($method) {
        case 'all':
            return collect($response->pokemon);
            break;
        case 'get':
            return $response->pokemon;
            break;
    }
}
```

Another usage could be to cast certain fields are  data types. In this example, we'll cast any fields called `created_at` or `updated_at` to Carbon isntances

```php
public function parse($action, $response)
{
    $date_fields = [
        'created_at',
        'updated_at',
    ];

    $response = $response->pokemon;

    foreach ($date_fields as $date_field) {
        if (property_exists($response, $date_field)) {
            $response->{$date_field} = Carbon::parse($response->{$date_field});
        }
    }

    return $response;
}
```

# Sending parameters and headers

As said before, `Snorlax` is built on top of `Guzzle`, so it works basically the same way on passing headers, query strings and request bodies.

```php
<?php

$pokemons = $client->pokemons->all([
    'query' => [
        'sort' => 'name',
        'offset' => 0,
        'limit' => 150,
    ],
    'headers' => [
        'X-Foo' => 'Bar',
    ],
]);

$pokemons = $client->pokemons->create([
    'body' => [
        'name' => 'Ivysaur',
        'attacks' => [
            'Tackle',
            'Leer',
        ],
    ],
]);
```

# Changing client options

If you want to set default headers for every request you send to the default guzzle client, just use the `headers` options on your `params` config key, just like the [Guzzle docs](http://guzzle.readthedocs.org/en/latest/request-options.html#headers).

```php
<?php

$client = new Snorlax\RestClient([
    'client' => [
        'params' => [
            'headers' => [
                'X-Foo' => 'Bar',
            ],
            'defaults' => [
                'debug' => true,
            ],
            'cache' => true,
        ],
    ],
]);
```

# Setting a base URI

If all your resources are under the same base URI, you can pass it on the constructor instead of declaring on the `resource` class.

```php
<?php

$client = new Snorlax\RestClient([
    'client' => [
        'params' => [
            'base_uri' => 'http://localhost/api',
        ],
    ],
]);
```

# API Authorization

`Snorlax` supports two types of Authorization for now: `Bearer` and `Basic`. They're pretty easy to use.

```php
<?php

$client = new Snorlax\RestClient([
    // ...
]);
// Basic authorization
$client->setAuthMethod(new Snorlax\Auth\BasicAuth('user', 'password'));
// Bearer authorization
$client->setAuthMethod(new Snorlax\Auth\BearerAuth('your token'));
```

# Using a custom client

If you don't want to use `Guzzle`'s default client (or want to mock one), `Snorlax` accepts any class that implements `GuzzleHttp\ClientInterface`, so just pass your custom client in the constructor. Can be an instance or a callable.

```php
<?php

class MyOwnClient implements GuzzleHttp\ClientInterface
{
    private $config;

    public function __construct(array $params)
    {
        $this->config = $params;
    }
}

// Using a callable to instantiate a new client everytime
$client = new Snorlax\RestClient([
    'client' => [
        'custom' => function(array $params) {
            return new MyOwnClient($params);
        },
        'params' => [
            'param1' => 'value',
        ],
    ],
]);

$client = new Snorlax\RestClient([
    'client' => [
        'custom' => new MyOwnClient([
            'param1' => 1,
        ]),
    ],
]);
```

# Contributing
Please see the [CONTRIBUTING.md](.github/CONTRIBUTING.md) file for guidelines.

# LICENSE
[The MIT License (MIT)](LICENSE)

Copyright (c) 2016 EzDelivery
