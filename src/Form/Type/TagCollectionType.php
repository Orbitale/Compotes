<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Tag;
use App\Form\DataTransformer\UniqueTagsListTransformer;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagCollectionType extends AbstractType
{
    private $tagsRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagsRepository = $tagRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new UniqueTagsListTransformer(), true)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('entry_type', TagEntityType::class)
            ->setDefault('entry_options', [
                'label' => false,
            ])
            ->setDefault('allow_add', true)
            ->setDefault('allow_delete', true)
            ->setDefault('delete_empty', true)
        ;

    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
