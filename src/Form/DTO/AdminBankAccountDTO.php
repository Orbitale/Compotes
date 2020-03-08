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

use App\Entity\BankAccount;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TODO: Can't be used before a decision is made on https://github.com/symfony/symfony/issues/22592.
 *
 * @-UniqueEntity(fields={"slug"}, entityClass="App\Entity\BankAccount")
 */
class AdminBankAccountDTO implements EasyAdminDTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    public $currency = 'EUR';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private ?string $name = null;

    private string $slug;

    private function __construct()
    {
    }

    /**
     * @param BankAccount $entity
     */
    public static function createFromEntity(object $entity): EasyAdminDTOInterface
    {
        return self::fromBankAccount($entity);
    }

    public static function createEmpty(): self
    {
        return new self();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
        $this->slug = (new AsciiSlugger())->slug($name)->toString();
    }

    private static function fromBankAccount(BankAccount $entity): self
    {
        $self = new self();

        $self->name = $entity->getName();
        $self->currency = $entity->getCurrency();
        $self->slug = $entity->getSlug();

        return $self;
    }
}
