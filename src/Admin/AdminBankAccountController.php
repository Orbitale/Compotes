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

use App\Entity\BankAccount;
use App\Form\DTO\AdminBankAccountDTO;
use App\Form\DTO\EasyAdminDTOInterface;
use Closure;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminBankAccountController extends EasyAdminController
{
    use BaseDTOControllerTrait;

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    protected function getDTOClass(): string
    {
        return AdminBankAccountDTO::class;
    }

    /**
     * @param AdminBankAccountDTO $dto
     */
    protected function createEntityFromDTO(EasyAdminDTOInterface $dto): object
    {
        $slugger = $this->getSluggerCallback();

        return BankAccount::fromAdmin($dto, $slugger);
    }

    /**
     * @param BankAccount         $entity
     * @param AdminBankAccountDTO $dto
     */
    protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object
    {
        return $this->doUpdateEntityWithDTO($entity, $dto);
    }

    private function doUpdateEntityWithDTO(BankAccount $entity, AdminBankAccountDTO $dto): BankAccount
    {
        $entity->updateFromAdmin($dto);

        return $entity;
    }

    private function getSluggerCallback(): Closure
    {
        $slugger = $this->slugger;

        return static function (string $name) use ($slugger) {
            return $slugger->slug($name)->toString();
        };
    }
}
