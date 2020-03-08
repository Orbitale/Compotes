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

namespace App\DataFixtures;

use App\Entity\BankAccount;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\DoctrineTools\AbstractFixture;

class BankAccountFixtures extends AbstractFixture implements ORMFixtureInterface
{
    protected function getEntityClass(): string
    {
        return BankAccount::class;
    }

    protected function getReferencePrefix(): ?string
    {
        return 'bank-account-';
    }

    protected function getMethodNameForReference(): string
    {
        return 'getSlug';
    }

    protected function getObjects(): array
    {
        $default = $this->repo->findOneBy(['slug' => 'default']);

        if ($default) {
            // Default can be present since it's in the migrations.
            $this->manager->remove($default);
            $this->manager->flush();
        }

        return [
            [
                'id' => 1,
                'name' => 'Default account',
                'slug' => 'default',
                'currency' => 'EUR',
            ],
        ];
    }
}
