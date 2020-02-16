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

namespace App\Operations;

use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class TriageSynchronizer
{
    private OperationRepository $operationRepository;
    private TagRepository $tagRepository;
    private EntityManagerInterface $em;

    public function __construct(
        OperationRepository $operationRepository,
        TagRepository $tagRepository,
        EntityManagerInterface $em
    ) {
        $this->operationRepository = $operationRepository;
        $this->tagRepository = $tagRepository;
        $this->em = $em;
    }

    public function syncOperations(): int
    {
        $operations = $this->operationRepository->findWithSameHashes();

        foreach ($operations as $operation) {
            $operation->flagForTriage();
            $this->em->persist($operation);
        }

        $this->em->flush();

        return \count($operations);
    }
}
