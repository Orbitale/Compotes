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

namespace App\Controller\Admin;

use App\Form\DTO\AdminTagDTO;
use App\Form\DTO\EasyAdminDTOInterface;
use App\Form\DTO\TranslatableDTOInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Form\FormInterface;

trait BaseDTOControllerTrait
{
    private TranslationRepository $translationRepository;
    private array $locales;

    /**
     * @required
     */
    public function setTranslationRepository(TranslationRepository $translationRepository): void
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * @required
     */
    public function setLocales(array $locales): void
    {
        $this->locales = $locales;
    }

    /**
     * @return
     */
    abstract protected function getDTOClass(): string;

    /**
     * @return object an instance of the configured Entity
     */
    abstract protected function createEntityFromDTO(EasyAdminDTOInterface $dto): object;

    /**
     * You can return a new instance if you want to override any existing entity instead of updating one (i.e when you have immutable objects).
     *
     * @return object an instance of the configured Entity, or the same object that is passed as argument
     */
    abstract protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object;

    /**
     * @return EasyAdminDTOInterface
     */
    public function createEntity(string $entityFqcn)
    {
        /** @var EasyAdminDTOInterface $dtoClass */
        $dtoClass = $this->doGetDTOClass();

        return $dtoClass::createEmpty();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $dtoClass = $this->doGetDTOClass();

        if (!($entityInstance instanceof $dtoClass)) {
            throw new InvalidArgumentException('DTO is not of valid type.');
        }

        $entityManager->persist($this->createEntityFromDTO($entityInstance));
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $dtoClass = $this->doGetDTOClass();

        if (!($entityInstance instanceof $dtoClass)) {
            throw new InvalidArgumentException(\sprintf('DTO is not of valid type, expected %s.', $dtoClass));
        }

        dd($entityInstance);

        $updatedEntity = $this->updateEntityWithDTO($entity, $dto);

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions): FormInterface
    {
        $formOptions->set('data_class', $this->doGetDTOClass());

        return parent::createNewForm($entityDto, $formOptions);
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions): FormInterface
    {
        $formOptions->set('data_class', AdminTagDTO::class);

        $entity = $entityDto->getInstance();
        dd($entity);

        $options = [];

        if (\is_subclass_of(AdminTagDTO::class, TranslatableDTOInterface::class, true)) {
            $options['translations'] = $this->translationRepository->findTranslations($entity);
            $options['locales'] = $this->locales;
        }

        $dtoClass = $this->getDTOClass();
        $instance = $entityDto->getInstance();
        $entityDto->setInstance($dtoClass::createFromEntity($instance));
        $formOptions->set('data', $instance);

        return parent::createEditForm($entityDto, $formOptions);
    }

    protected function createDTOFromEntity(object $entity): EasyAdminDTOInterface
    {
        /** @var EasyAdminDTOInterface $dtoClass */
        $dtoClass = $this->getDTOClass();

        $options = [];

        if (\is_subclass_of($dtoClass, TranslatableDTOInterface::class, true)) {
            $options['translations'] = $this->translationRepository->findTranslations($entity);
            $options['locales'] = $this->locales;
        }

        return $dtoClass::createFromEntity($entity, $options);
    }

    protected function translateEntityFieldsFromDTO(object $entity, EasyAdminDTOInterface $dto): void
    {
        if ($dto instanceof TranslatableDTOInterface) {
            foreach ($this->locales as $locale) {
                foreach ($dto::getTranslatableFields() as $entityField => $dtoField) {
                    $this->translationRepository->translate($entity, $entityField, $locale, $dto->{$dtoField}[$locale]);
                }
            }
        }
    }

    /**
     * @return string A class that must implement EasyAdminDTOInterface
     */
    private function doGetDTOClass(): string
    {
        $this->validateDTOClass();

        return $this->getDTOClass();
    }

    private function validateDTOClass(): void
    {
        if (!($this instanceof CrudControllerInterface)) {
            throw new RuntimeException(\sprintf('The DTO-based controller %s must extend %s.', \get_class($this), EasyAdminController::class));
        }

        $dtoClass = $this->getDTOClass();
        if (!\is_subclass_of($dtoClass, EasyAdminDTOInterface::class, true)) {
            throw new RuntimeException(\sprintf('DTO class %s must implement %s.', $dtoClass, EasyAdminDTOInterface::class));
        }
    }
}
