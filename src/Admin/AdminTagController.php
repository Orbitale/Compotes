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
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

class AdminTagController extends EasyAdminController
{
    use BaseDTOControllerTrait;

    private TranslationRepository $translationRepository;
    private array $locales;

    public function __construct(
        TranslationRepository $translationRepository,
        array $locales
    ) {
        $this->translationRepository = $translationRepository;
        $this->locales = $locales;
    }

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
     * @param Tag         $entity
     * @param AdminTagDTO $dto
     */
    protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object
    {
        $entity->updateFromAdmin($dto);

        foreach ($this->locales as $locale) {
            $this->translationRepository->translate($entity, 'name', $locale, $dto->translatedNames[$locale]);
        }

        return $entity;
    }

    protected function createDTOFromEntity(object $entity): EasyAdminDTOInterface
    {
        $class = $this->doGetDTOClass();

        $options = [
            'translations' => $this->translationRepository->findTranslations($entity),
            'locales' => $this->locales,
        ];

        return $class::createFromEntity($entity, $options);
    }
}
