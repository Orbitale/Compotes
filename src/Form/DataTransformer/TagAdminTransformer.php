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

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagAdminTransformer implements DataTransformerInterface
{
    private TagRepository $tagsRepo;
    private EntityManagerInterface $em;

    public function __construct(TagRepository $tagsRepo, EntityManagerInterface $em)
    {
        $this->tagsRepo = $tagsRepo;
        $this->em = $em;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($submittedIds)
    {
        $existingTags = $this->tagsRepo->findIndexedById();

        $tags = [];

        $hasNewTags = false;

        foreach ($submittedIds as $k => $id) {
            if (isset($existingTags[$id])) {
                $tags[] = $existingTags[$id];
            } else {
                $tag = new Tag();
                $tag->setName($id);
                $this->em->persist($tag);
                $hasNewTags = true;
                $tags[] = $tag;
            }
        }

        if ($hasNewTags) {
            $this->em->flush();
        }

        return \array_map(function (Tag $tag) {
            return $tag->getId();
        }, $tags);
    }
}
