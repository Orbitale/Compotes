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

use App\Entity\Operation;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\Callback(callback="validate")
 */
class Triage
{
    /**
     * @Assert\NotBlank
     */
    public ?string $details;

    private DateTimeInterface $operationDate;
    private string $type;
    private float $amount;
    private string $initialDetails;

    private function __construct()
    {
    }

    public static function fromOperation(Operation $operation): self
    {
        $self = new self();

        $self->operationDate = $operation->getOperationDate();
        $self->type = $operation->getType();
        $self->amount = $operation->getAmount();
        $self->initialDetails = $operation->getDetails();
        $self->details = $operation->getDetails();

        return $self;
    }

    public function validate(ExecutionContextInterface $context): void
    {
        if (false === strpos($this->details, $this->initialDetails)) {
            $context
                ->buildViolation('admin.triage.validators.initials_details_must_not_be_lost')
                ->setTranslationDomain('messages')
                ->atPath('details')
                ->addViolation()
            ;
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOperationDate(): DateTimeInterface
    {
        return $this->operationDate;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getInitialDetails(): string
    {
        return $this->initialDetails;
    }

    public function hasChanged(): bool
    {
        return $this->details !== $this->initialDetails;
    }
}
