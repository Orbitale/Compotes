<?php


namespace App\DataFixtures;


use App\Entity\Operation;
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
                'operationDate' => new \DateTimeImmutable(),
                'type' => 'EXAMPLE TYPE',
                'typeDisplay' => 'Example display type',
                'details' => 'THIS IS JUST AN EXAMPLE',
                'amountInCents' => 25000,
            ],
        ];
    }
}
