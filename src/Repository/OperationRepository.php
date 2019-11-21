<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function monthIsPopulated(\DateTimeImmutable $month): bool
    {
        $firstDay = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', sprintf(
            '%s-%s-1 00:00:00 +000',
            $month->format('Y'),
            $month->format('m')
        ));

        $lastDay = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', sprintf(
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
}
