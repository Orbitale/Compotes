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

use App\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

class AdminTagDTO implements EasyAdminDTOInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\All(constraints={
     *     @Assert\NotBlank(),
     *     @Assert\Type("string")
     * })
     */
    public ?array $translatedNames = [];

    /**
     * @Assert\Type(App\Entity\Tag::class)
     */
    public ?Tag $parent = null;

    /**
     * @param Tag $entity
     */
    public static function createFromEntity(object $entity, array $options = []): EasyAdminDTOInterface
    {
        $self = new self();

        [
            'translations' => $translations,
            'locales' => $locales,
        ] = $options;

        if ($translations) {
            foreach ($translations as $locale => $values) {
                $self->translatedNames[$locale] = $values['name'];
            }
        }

        foreach ($locales as $locale) {
            if (isset($self->translatedNames[$locale])) {
                continue;
            }
            $self->translatedNames[$locale] = $entity->getName();
        }

        $self->parent = $entity->getParent();

        return $self;
    }

    /**
     * @return AdminTagDTO|EasyAdminDTOInterface
     */
    public static function createEmpty(): EasyAdminDTOInterface
    {
        return new self();
    }
}
