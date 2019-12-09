<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRuleRepository")
 * @ORM\Table(name="tag_rules")
 */
class TagRule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="tag_rule_tag",
     *     joinColumns={@ORM\JoinColumn(name="tag_rule_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tags;

    /**
     * @ORM\Column(name="matching_pattern", type="text")
     */
    private $matchingPattern;

    /**
     * @ORM\Column(name="is_regex", type="boolean", options={"default": "1"})
     */
    private $regex = true;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getMatchingPattern(): ?string
    {
        return $this->matchingPattern;
    }

    public function isRegex(): bool
    {
        return $this->regex;
    }

    public function setTags(ArrayCollection $tags): void
    {
        $this->tags = $tags;
    }

    public function setMatchingPattern(string $matchingPattern): void
    {
        $this->matchingPattern = $matchingPattern;
    }

    public function setIsRegex(bool $regex): void
    {
        $this->regex = $regex;
    }
}
