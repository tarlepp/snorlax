<?php

namespace Snorlax\Auth;

/**
 * Implementation of Authorization via the basic authorization method on the API
 */
class BasicAuth implements Authorization
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return base64_encode(sprintf('%s:%s', $this->user, $this->password));
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthType()
    {
        return 'Basic';
    }
}
