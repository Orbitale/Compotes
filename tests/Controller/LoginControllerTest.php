<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function test successful login()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        static::markTestIncomplete('Must be able to create a test database to finish this, but Symfony CLI is not compatible with that for now...');
    }
}
