<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    public function test root redirects to admin()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('http://localhost/admin/', 302);
    }
}
