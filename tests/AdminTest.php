<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    use BrowserLoginTrait;

    public function test root redirects to admin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('http://localhost/admin/', 302);
    }

    public function test root admin page while not logged in redirects to login(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertResponseRedirects('http://localhost/login', 302);
    }

    public function test root admin page while logged as standard user returns 403(): void
    {
        $client = static::createClient();
        $this->login($client, 'test-user', ['ROLE_USER']);
        $client->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(403);
    }

    public function test root admin page while logged as admin user redirects to admin homepage(): void
    {
        $client = static::createClient();
        $this->login($client, 'test-user', ['ROLE_ADMIN']);
        $client->request('GET', '/admin/');

        $this->assertResponseRedirects('/admin/analytics?menuIndex=1&submenuIndex=-1', 302);
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }
}
