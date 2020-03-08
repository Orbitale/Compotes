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

namespace App\Tests\Admin;

use App\Entity\BankAccount;
use App\Repository\BankAccountRepository;
use App\Tests\BrowserLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminBankAccountController extends WebTestCase
{
    use BrowserLoginTrait;

    public function test new account(): void
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/admin/?entity=BankAccount&action=new');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Save changes', [
            'bankaccount[name]' => $accountName = 'Test account',
            'bankaccount[currency]' => $currency = 'EUR',
        ]);

        $this->assertResponseRedirects('/admin/?action=list&entity=BankAccount');

        /** @var BankAccountRepository $repo */
        $repo = static::$container->get(BankAccountRepository::class);

        $account = $repo->findOneBy(['slug' => 'test-account']);

        static::assertInstanceOf(BankAccount::class, $account);
        static::assertSame($accountName, $account->getName());
        static::assertSame($currency, $account->getCurrency());
    }

    public function test edit account(): void
    {
        $client = static::createClient();
        $this->login($client);

        /** @var BankAccountRepository $repo */
        $repo = static::$container->get(BankAccountRepository::class);

        /** @var BankAccount $account */
        $account = $repo->findOneBy([]);
        $existingSlug = $account->getSlug();
        static::assertInstanceOf(BankAccount::class, $account);

        $client->request('GET', \sprintf('/admin/?entity=BankAccount&action=edit&id=%s', $account->getId()));
        unset($account);

        $this->assertResponseIsSuccessful();

        static::$container->get(EntityManagerInterface::class)->clear();

        $client->submitForm('Save changes', [
            'bankaccount[name]' => $accountName = 'New account name '.\uniqid('', true),
            'bankaccount[currency]' => $currency = 'USD',
        ]);

        $this->assertResponseRedirects('/admin/?action=list&entity=BankAccount');

        $account = $repo->findOneBy(['slug' => $existingSlug]);
        static::assertSame($accountName, $account->getName());
        static::assertSame($currency, $account->getCurrency());
    }
}
