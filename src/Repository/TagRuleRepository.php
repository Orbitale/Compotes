<?php

namespace App\Repository;

use App\Entity\TagRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TagRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagRule[]    findAll()
 * @method TagRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagRule::class);
    }
}
