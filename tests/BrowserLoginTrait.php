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

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

trait BrowserLoginTrait
{
    public function logout(KernelBrowser $browser, string $firewallName = 'main'): void
    {
        $context = $this->getFirewallContext($browser, $firewallName);

        $browser->getContainer()->get('session')->remove('_security_'.$context);
    }

    /**
     * @param string|UserInterface $user
     */
    public function login(
        KernelBrowser $browser,
        $user = 'admin',
        array $roles = [],
        string $firewallName = 'main'
    ): TokenInterface {
        if (!\class_exists(UsernamePasswordToken::class)) {
            throw new RuntimeException('You must install the "symfony/security-core" component to use this feature.');
        }

        if (\is_string($user)) {
            $user = static::$container->get(UserProviderInterface::class)->loadUserByUsername($user);
            if (!$user) {
                static::fail(\sprintf('Cannot find user %s', $user));
            }
        }

        $roles = \array_merge($user->getRoles(), $roles);

        $token = $this->getLoginToken($browser, $user, $roles, $firewallName);

        $this->authenticateToken($browser, $token, $firewallName);

        return $token;
    }

    protected function getLoginToken(KernelBrowser $browser, $user, array $roles = [], string $firewallName = 'main'): TokenInterface
    {
        return new UsernamePasswordToken($user, null, $firewallName, $roles);
    }

    protected function authenticateToken(KernelBrowser $browser, TokenInterface $token, string $firewallName = 'main'): void
    {
        $context = $this->getFirewallContext($browser, $firewallName);

        $session = $browser->getContainer()->get('session');
        $session->set('_security_'.$context, \serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $browser->getCookieJar()->set($cookie);
    }

    private function getFirewallContext(KernelBrowser $browser, string $firewallName = 'main'): string
    {
        $config = \sprintf('security.firewall.map.config.%s', $firewallName);

        if (!$browser->getContainer()->has($config)) {
            if (static::$container instanceof TestContainer && static::$container->has($config)) {
                return static::$container->get($config)->getContext();
            }

            throw new RuntimeException(\sprintf('Firewall "%s" does not exists or is not a public service.', $firewallName));
        }

        return $browser->getContainer()->get($config)->getContext();
    }
}
