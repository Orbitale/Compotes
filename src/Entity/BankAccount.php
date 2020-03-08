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

namespace App\Entity;

use App\Form\DTO\AdminBankAccountDTO;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BankAccountRepository")
 * @ORM\Table(name="bank_accounts")
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 */
class BankAccount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private string $currency;

    private function __construct()
    {
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public static function fromAdmin(AdminBankAccountDTO $dto, callable $slugger): self
    {
        $self = new self();

        $self->name = $dto->getName();
        $self->slug = $slugger($self->name);
        $self->currency = $dto->currency;

        return $self;
    }

    public function updateFromAdmin(AdminBankAccountDTO $dto): void
    {
        $this->name = $dto->getName();
        $this->currency = $dto->currency;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
