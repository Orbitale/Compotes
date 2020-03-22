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
use App\Form\DTO\AdminTagDTO;
use App\Form\DTO\EasyAdminDTOInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class AdminTagController extends EasyAdminController
{
    use BaseDTOControllerTrait;

    protected function getDTOClass(): string
    {
        return AdminTagDTO::class;
    }

    /**
     * @param AdminTagDTO $dto
     */
    protected function createEntityFromDTO(EasyAdminDTOInterface $dto): object
    {
        return Tag::fromAdmin($dto);
    }

    /**
     * @param Tag $entity
     */
    protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object
    {
        $entity->updateFromAdmin($dto);

        return $entity;
    }
}
