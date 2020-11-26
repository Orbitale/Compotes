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

namespace App\Admin;

use App\Entity\Tag;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminOperationController extends EasyAdminController
{
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null): QueryBuilder
    {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        return $this->joinTags($qb);
    }

    protected function renderTemplate($actionName, $templatePath, array $parameters = [])
    {
        if ('list' === $actionName) {
            $parameters['tags_form'] = $this->container
                ->get('form.factory')
                ->createNamed('operation_tags', EntityType::class, [], [
                    'class' => Tag::class,
                    'attr' => ['data-widget' => 'select2'],
                    'multiple' => true,
                    'required' => false,
                ])
                ->createView()
            ;
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    protected function createSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        $qb = parent::createSearchQueryBuilder($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);

        return $this->joinTags($qb);
    }

    protected function joinTags(QueryBuilder $qb): QueryBuilder
    {
        $qb
            ->leftJoin('entity.tags', 'tags')
            ->addSelect('tags')
        ;

        return $qb;
    }
}
