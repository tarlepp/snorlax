<?php

namespace Snorlax\Auth;

/**
 * Implementation of Authorization via the "Authorization: Bearer" method
 */
class BearerAuth implements Authorization
{
    /**
     * @var string
     */
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return $this->token;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthType()
    {
        return 'Bearer';
    }
}
