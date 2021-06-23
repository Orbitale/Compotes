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

use App\Entity\Operation;
use App\Form\DTO\Triage;
use App\Form\Type\TriageType;
use App\Repository\OperationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class AdminTriageController extends EasyAdminController
{
    private OperationRepository $operationRepository;

    public function __construct(OperationRepository $operationRepository)
    {
        $this->operationRepository = $operationRepository;
    }

    /** @param Operation $entity */
    protected function createEntityFormBuilder($entity, $view): FormBuilderInterface
    {
        if ('edit' !== $view) {
            throw new InvalidArgumentException('Only edit view is supported.');
        }

        $dto = Triage::fromOperation($entity);

        $formOptions = $this->executeDynamicMethod('get<EntityName>EntityFormOptions', [$dto, $view]);

        return $this->get('form.factory')->createNamedBuilder(
            mb_strtolower($this->entity['name']),
            TriageType::class,
            $dto,
            $formOptions
        );
    }

    /** @param Operation $entity */
    protected function updateEntity($entity, FormInterface $form = null): void
    {
        if (!$form) {
            throw new RuntimeException('Form must be injected by EasyAdmin. Did you mess up something?');
        }

        /** @var Triage $dto */
        $dto = $form->getData();

        if (!$dto->hasChanged()) {
            return;
        }

        $previousHash = $entity->getHash();

        $entity->updateFromTriage($dto);
        $this->em->persist($entity);

        $this->endTriage($entity->getId(), $previousHash);

        $this->em->flush();
    }

    /** @param Operation $entity */
    protected function removeEntity($entity): void
    {
        $previousHash = $entity->getHash();

        $this->em->remove($entity);

        $this->endTriage($entity->getId(), $previousHash);

        $this->em->flush();
    }

    private function endTriage(int $baseOperationId, string $previousHash): void
    {
        $operations = $this->operationRepository->findToEndTriage($baseOperationId, $previousHash);

        if (1 === \count($operations)) {
            $operations[0]->triageDone();
            $this->em->persist($operations[0]);
        }
    }
}
