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

namespace App\Repository;

use App\Entity\Operation;
use App\Form\DTO\AnalyticsFilters;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Operation find($id, $lockMode = null, $lockVersion = null)
 * @method null|Operation findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function monthIsPopulated(DateTimeImmutable $month): bool
    {
        $firstDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', sprintf(
            '%s-%s-1 00:00:00 +000',
            $month->format('Y'),
            $month->format('m')
        ));

        $lastDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', sprintf(
            '%s-%s-%s 23:59:59 +000',
            $firstDay->format('Y'),
            $firstDay->format('m'),
            $firstDay->format('t')
        ));

        $count = $this->createQueryBuilder('operation')
            ->select('count(operation)')
            ->where('operation.operationDate >= :first_day')
            ->andWhere('operation.operationDate <= :last_day')
            ->setParameter('first_day', $firstDay)
            ->setParameter('last_day', $lastDay)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @return Operation[]
     */
    public function findWithSameHashes(): array
    {
        return $this->_em->createQuery(
            <<<DQL
                SELECT operation
                FROM {$this->_entityName} operation
                WHERE operation.hash IN (
                    SELECT op2.hash
                    FROM {$this->_entityName} op2
                    GROUP BY op2.hash
                    HAVING count(op2) > 1
                )
            DQL
        )
            ->getResult()
        ;
    }

    /**
     * @return Operation[]
     */
    public function findToEndTriage(int $baseOperationId, string $previousHash): array
    {
        return $this->_em->createQuery(
            <<<DQL
                SELECT operation
                FROM {$this->_entityName} operation
                WHERE operation.hash = :hash
                AND operation.id != :id
            DQL
        )
            ->setParameter('id', $baseOperationId)
            ->setParameter('hash', $previousHash)
            ->getResult()
        ;
    }

    /**
     * @return Operation[]
     */
    public function findForAnalytics(AnalyticsFilters $filters): array
    {
        $qb = $this->createQueryBuilder('operation')
            ->addSelect('tags')
            ->where('operation.ignoredFromCharts = false')
            ->leftJoin('operation.tags', 'tags')
        ;

        if ($filters->startDate) {
            $qb
                ->andWhere('operation.operationDate >= :startDate')
                ->setParameter('startDate', $filters->startDate)
            ;
        }
        if ($filters->endDate) {
            $qb
                ->andWhere('operation.operationDate <= :endDate')
                ->setParameter('endDate', $filters->endDate)
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
