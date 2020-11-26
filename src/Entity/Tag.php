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

use ApiPlatform\Core\Annotation\ApiResource;
use App\Form\DTO\AdminTagDTO;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="tags")
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Gedmo\Translatable()
     */
    private string $name = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private ?Tag $parent = null;

    /**
     * @Gedmo\Locale()
     */
    private ?string $locale = null;

    public function __toString(): string
    {
        $names = [];

        $parent = $this->parent;

        while ($parent) {
            \array_unshift($names, $parent->getName());

            $parent = $parent->getParent();
        }

        $names[] = $this->name;

        return \implode(' > ', $names);
    }

    public static function fromAdmin(AdminTagDTO $dto): self
    {
        $self = new self();

        $self->populate($dto);

        return $self;
    }

    public function updateFromAdmin(AdminTagDTO $dto): void
    {
        $this->populate($dto);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }

    public function setName(string $name): void
    {
        $this->name = (new AsciiSlugger())->slug($name)->toString();
    }

    private function populate(AdminTagDTO $dto): void
    {
        $this->locale = 'en';
        $this->setName($dto->translatedNames['en']);
        $this->parent = $dto->parent;
    }
}
