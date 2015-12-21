<?php

use Snorlax\Auth\BasicAuth;

/**
 * Tests for the auth option on Snorlax
 */
class BasicAuthTest extends TestCase
{
    public function testBearerAuth()
    {
        $token = base64_encode('user:password');

        $auth = new BasicAuth('user', 'password');

        $this->assertSame($token, $auth->getCredentials());
        $this->assertSame('Basic', $auth->getAuthType());
    }
}
