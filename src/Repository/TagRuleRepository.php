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

use App\Entity\TagRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method null|TagRule find($id, $lockMode = null, $lockVersion = null)
 * @method null|TagRule findOneBy(array $criteria, array $orderBy = null)
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
