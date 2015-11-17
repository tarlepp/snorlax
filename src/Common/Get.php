<?php

namespace Snorlax\Common;

trait Get
{
    public function all($params=[], $headers=[])
    {
        return $this->makeRequest($this->getBaseUri(), $params, $headers);
    }

    public function get($id, $params=[], $headers=[])
    {
        $uri = sprintf('%s/%s', $this->getBaseUri(), $id);

        return $this->makeRequest($uri, $params, $headers);
    }

    private function makeRequest($uri, $params, $headers)
    {
        $extra = [];
        if ($params) {
            $extra['query'] = $params;
        }

        if ($headers) {
            $extra['headers'] = $headers;
        }

        return json_decode($this->client->get($uri, $extra)->getBody());
    }
}
