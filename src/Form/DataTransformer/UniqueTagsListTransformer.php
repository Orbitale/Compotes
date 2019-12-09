<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Symfony\Component\Form\DataTransformerInterface;
use function array_values;

class UniqueTagsListTransformer implements DataTransformerInterface
{
    public function transform($tags)
    {
        return $tags;
    }

    public function reverseTransform($tags): array
    {
        if (!$tags) {
            return [];
        }

        $uniqueTagsById = [];

        foreach ($tags as $tag) {
            /** @var Tag $tag */
            $uniqueTagsById[$tag->getId()] = $tag;
        }

        return array_values($uniqueTagsById);
    }
}
