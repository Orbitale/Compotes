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

use App\Form\DTO\Triage;
use App\Model\ImportOptions;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use LogicException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 * @ORM\Table(name="operations")
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 */
class Operation
{
    public const STATE_OK = 'ok';
    public const STATE_PENDING_TRIAGE = 'pending_triage';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="operation_date", type="datetime_immutable")
     */
    private DateTimeImmutable $operationDate;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(name="type_display", type="string", length=255)
     */
    private string $typeDisplay = '';

    /**
     * @ORM\Column(name="details", type="text")
     */
    private string $details = '';

    /**
     * Always in cents.
     * To get float value, use self::getAmount.
     *
     * @ORM\Column(name="amount_in_cents", type="integer")
     */
    private int $amountInCents;

    /**
     * @ORM\Column(name="hash", type="string")
     */
    private string $hash;

    /**
     * @ORM\Column(name="state", type="string", options={"default" = "ok"})
     */
    private string $state = self::STATE_OK;

    /**
     * @ORM\Column(name="ignored_from_charts", type="boolean", options={"default" = "0"})
     */
    private bool $ignoredFromCharts = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BankAccount", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private BankAccount $bankAccount;

    /**
     * @var ArrayCollection|Tag[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     */
    private $tags;

    private function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public static function fromImportLine(BankAccount $bankAccount, array $line, string $dateFormat = ImportOptions::OPERATION_DATE_FORMAT): self
    {
        $self = new self();

        $self->bankAccount = $bankAccount;

        $date = DateTimeImmutable::createFromFormat($dateFormat, sprintf(
            '%s 00:00:00 +000',
            $line['date']
        ));

        if (false === $date) {
            throw new InvalidArgumentException(sprintf(
                'Operation date was expected to be a valid date respecting the "%s" format, "%s" given.',
                ImportOptions::OPERATION_DATE_FORMAT,
                $line['date'],
            ));
        }

        $self->operationDate = $date;

        $self->type = $line['type'];
        $self->typeDisplay = $line['type_display'];
        $self->details = (string) preg_replace('~\s+~', ' ', $line['details']);

        $amount = preg_replace('~[^0-9+-]+~', '', $line['amount']);

        if (!is_numeric($amount)) {
            throw new InvalidArgumentException(sprintf(
                'Operation amount was expected to be a number, "%s" given.',
                $line['amount'],
            ));
        }

        $self->amountInCents = (int) $amount;

        $self->hash = self::computeHash(
            $self->type,
            $self->typeDisplay,
            $self->details,
            $self->operationDate,
            $self->amountInCents,
            $self->bankAccount
        );

        return $self;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOperationDate(): DateTimeImmutable
    {
        return $this->operationDate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getTypeDisplay(): string
    {
        return $this->typeDisplay;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function getAmount(): float
    {
        return $this->amountInCents / 100;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getBankAccount(): BankAccount
    {
        return $this->bankAccount;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function flagForTriage(): void
    {
        $this->state = self::STATE_PENDING_TRIAGE;
    }

    public function isIgnoredFromCharts(): bool
    {
        return $this->ignoredFromCharts;
    }

    public function setIgnoredFromCharts(bool $value): void
    {
        $this->ignoredFromCharts = $value;
    }

    public function triageDone(): void
    {
        $this->state = self::STATE_OK;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    /**
     * @return bool true if rule was applied
     */
    public function applyRule(TagRule $rule): bool
    {
        $addedTags = false;

        $matches = $rule->isRegex()
            ? preg_match($rule->getMatchingPattern(), $this->details)
            : false !== stripos($this->details, $rule->getMatchingPattern())
        ;

        if ($matches) {
            foreach ($rule->getTags() as $tag) {
                if (!$this->tags->contains($tag)) {
                    $this->addTag($tag);
                    $addedTags = true;
                }
            }
        }

        return $addedTags;
    }

    public function updateFromTriage(Triage $dto): void
    {
        if (!$this->isPendingTriage()) {
            throw new LogicException('Cannot update an operation after triage if operation itself is not pending triage.');
        }

        $this->details = (string) $dto->details;
        $this->triageDone();
        $this->recomputeHash();
    }

    public static function computeHash(
        string $type,
        string $typeDisplay,
        string $details,
        DateTimeInterface $operationDate,
        int $amountInCents,
        BankAccount $bankAccount
    ): string {
        $str =
            $type.
            '_'.$bankAccount->getSlug().
            '_'.$typeDisplay.
            '_'.$details.
            '_'.$operationDate->format('Y-m-d_H:i:s').
            '_'.$amountInCents
        ;

        return hash('sha512', $str);
    }

    private function isPendingTriage(): bool
    {
        return self::STATE_PENDING_TRIAGE === $this->state;
    }

    private function recomputeHash(): void
    {
        $this->hash = self::computeHash(
            $this->type,
            $this->typeDisplay,
            $this->details,
            $this->operationDate,
            $this->amountInCents,
            $this->bankAccount
        );
    }
}
