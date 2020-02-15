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

use App\Entity\Operation;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\DoctrineTools\AbstractFixture;

class OperationFixtures extends AbstractFixture implements ORMFixtureInterface
{
    protected function getEntityClass(): string
    {
        return Operation::class;
    }

    protected function getObjects(): array
    {
        return [
            [
                'operationDate' => new DateTimeImmutable(),
                'type' => 'EXAMPLE TYPE',
                'typeDisplay' => 'Example display type',
                'details' => 'THIS IS JUST AN EXAMPLE',
                'amountInCents' => 25000,
                'hash' => function (Operation $operation) {
                    return Operation::computeHash(
                        $operation->getType(),
                        $operation->getTypeDisplay(),
                        $operation->getDetails(),
                        $operation->getOperationDate(),
                        $operation->getAmountInCents()
                    );
                },
            ],
        ];
    }
}
