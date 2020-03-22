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

namespace App\Form\DTO;

interface EasyAdminDTOInterface
{
    /**
     * @return static
     */
    public static function createFromEntity(object $entity, array $options = []): self;

    /**
     * @return static
     */
    public static function createEmpty();
}
