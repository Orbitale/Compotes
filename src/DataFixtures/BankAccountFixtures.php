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
use App\Repository\BankAccountRepository;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

class BankAccountFixtures extends ArrayFixture implements ORMFixtureInterface
{
    private EntityManagerInterface $em;
    private BankAccountRepository $repo;

    public function __construct(EntityManagerInterface $em, BankAccountRepository $repo)
    {
        parent::__construct();
        $this->em = $em;
        $this->repo = $repo;
    }

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
            $this->em->remove($default);
            $this->em->flush();
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
