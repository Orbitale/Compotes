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

namespace App\Tests\Controller;

use App\Tests\BrowserLoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LoginControllerTest extends WebTestCase
{
    use BrowserLoginTrait;

    public function test successful login(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        $client->followRedirects();

        $client->submitForm('Sign in', [
            '_username' => 'admin',
            '_password' => 'admin',
        ]);

        $this->assertSelectorTextSame('.user .user-name', 'admin');
    }

    public function test login page whiled logged in redirects to admin(): void
    {
        $client = static::createClient();
        $this->login($client, static::$container->get(UserProviderInterface::class)->loadUserByUsername('admin'));
        $client->request('GET', '/login');

        $this->assertResponseRedirects('/admin/', 302);
    }
}
