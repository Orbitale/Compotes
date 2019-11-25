<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;
use function array_diff;
use function array_filter;
use function array_map;
use function array_unique;
use function explode;
use function implode;

class TagArrayToStringTransformer implements DataTransformerInterface
{
    private $tagsRepository;

    public function __construct(TagRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($tags): string
    {
        /* @var Tag[] $tags */
        return implode(', ', $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string): array
    {
        if ('' === $string || null === $string) {
            return [];
        }

        $names = array_unique(array_filter(array_map('trim', explode(',', $string))));

        // Get the current tags and find the new ones that should be created.
        $tags = $this->tagsRepository->findBy([
            'name' => $names,
        ]);

        foreach (array_diff($names, $tags) as $name) {
            $tags[] = Tag::create($name);
        }

        return $tags;
    }
}
