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

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 * @ORM\Table(name="operations")
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 */
class Operation
{
    public const DEFAULT_DATE_FORMAT = 'd/m/Y H:i:s O';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="operation_date", type="datetime_immutable")
     */
    private $operationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="type_display", type="string", length=255)
     */
    private $typeDisplay;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text")
     */
    private $details;

    /**
     * Always in cents.
     * To get float value, use self::getAmount.
     *
     * @var int
     *
     * @ORM\Column(name="amount_in_cents", type="integer")
     */
    private $amountInCents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public static function fromImportLine(array $line): self
    {
        $self = new self();

        $date = DateTimeImmutable::createFromFormat(self::DEFAULT_DATE_FORMAT, \sprintf(
            '%s 00:00:00 +000',
            $line['date']
        ));

        if (false === $date) {
            throw new InvalidArgumentException(\sprintf(
                'Operation date was expected to be a valid date respecting the "%s" format, "%s" given.',
                self::DEFAULT_DATE_FORMAT,
                $line['date'],
            ));
        }

        $self->operationDate = $date;

        $self->type = $line['type'];
        $self->typeDisplay = $line['type_display'];
        $self->details = \preg_replace('~\s+~', ' ', $line['details']);

        $amount = \preg_replace('~[^0-9+-]+~', '', $line['amount']);

        if (!\is_numeric($amount)) {
            throw new InvalidArgumentException(\sprintf(
                'Operation amount was expected to be a number, "%s" given.',
                $line['amount'],
            ));
        }

        $self->amountInCents = (int) $amount;

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

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return bool true if rule was applied
     */
    public function applyRule(TagRule $rule): bool
    {
        $addedTags = false;

        $matches = $rule->isRegex()
            ? \preg_match($rule->getMatchingPattern(), $this->details)
            : false !== \stripos($this->details, $rule->getMatchingPattern())
        ;

        if ($matches) {
            foreach ($rule->getTags() as $tag) {
                if (!$this->tags->contains($tag)) {
                    $this->addTag($tag);
                    $addedTags = true;
                }
            }

            return $addedTags;
        }

        return $addedTags;
    }

    private function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }
}
