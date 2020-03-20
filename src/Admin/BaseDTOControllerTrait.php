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

use App\Form\DTO\EasyAdminDTOInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

trait BaseDTOControllerTrait
{
    abstract protected function getDTOClass(): string;

    /**
     * @return object an instance of the configured Entity
     */
    abstract protected function createEntityFromDTO(EasyAdminDTOInterface $dto): object;

    /**
     * You can return a new instance if you want to override any existing entity instead of updating one (i.e when you have immutable objects).
     *
     * @return null|object an instance of the configured Entity, or the same object that is passed as argument
     */
    abstract protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object;

    protected function createNewEntity(): EasyAdminDTOInterface
    {
        $dtoClass = $this->doGetDTOClass();

        return $dtoClass::createEmpty();
    }

    protected function persistEntity($object): void
    {
        $dtoClass = $this->doGetDTOClass();

        if (!($object instanceof $dtoClass)) {
            throw new InvalidArgumentException('DTO is not of valid type.');
        }

        parent::persistEntity($this->createEntityFromDTO($object));
    }

    protected function getEntityFormOptions($entity, $view): array
    {
        $dtoClass = $this->doGetDTOClass();

        $options = parent::getEntityFormOptions($entity, $view);
        $options['data_class'] = $dtoClass;

        return $options;
    }

    protected function createEntityFormBuilder($entity, $view): FormBuilderInterface
    {
        $dtoClass = $this->doGetDTOClass();

        if (!($entity instanceof $dtoClass)) {
            $entity = $this->createDTOFromEntity($entity);
        }

        return parent::createEntityFormBuilder($entity, $view);
    }

    protected function updateEntity($entity, FormInterface $editForm = null): object
    {
        if (!$editForm) {
            throw new InvalidArgumentException('Form is mandatory to update entity.');
        }

        $dtoClass = $this->doGetDTOClass();
        $entityClass = $this->entity['class'];
        $dto = $editForm->getData();

        if (!($dto instanceof $dtoClass)) {
            throw new InvalidArgumentException(\sprintf('DTO is not of valid type, expected %s.', $dtoClass));
        }

        if (!($entity instanceof $entityClass)) {
            throw new InvalidArgumentException(\sprintf('Only %s is supported in this controller.', $entityClass));
        }

        $updatedEntity = $this->updateEntityWithDTO($entity, $dto);

        parent::updateEntity($updatedEntity);

        return $updatedEntity;
    }

    protected function createDTOFromEntity(object $entity): EasyAdminDTOInterface
    {
        $class = $this->doGetDTOClass();

        return $class::createFromEntity($entity);
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
        if (!($this instanceof EasyAdminController)) {
            throw new RuntimeException(\sprintf('The DTO-based controller %s must extend %s.', \get_class($this), EasyAdminController::class));
        }

        $dtoClass = $this->getDTOClass();
        if (!\is_subclass_of($dtoClass, EasyAdminDTOInterface::class, true)) {
            throw new RuntimeException(\sprintf('DTO class %s must implement %s.', $dtoClass, EasyAdminDTOInterface::class));
        }
    }
}
