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

namespace App\Controller\Api;

use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AddTagsController
{
    private OperationRepository $operationRepository;
    private TagRepository $tagRepository;
    private EntityManagerInterface $em;
    private Environment $twig;

    public function __construct(OperationRepository $operationRepository, TagRepository $tagRepository, EntityManagerInterface $em, Environment $twig)
    {
        $this->operationRepository = $operationRepository;
        $this->tagRepository = $tagRepository;
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * @Route("/admin/api/operation/{id}/tags", name="api_operations_add_tags", methods={"POST"})
     */
    public function __invoke(string $id, Request $request)
    {
        $operation = $this->operationRepository->find($id);

        if (!$operation) {
            throw new NotFoundHttpException('Could not find operation with this id');
        }

        try {
            $tagsIds = \json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return new BadRequestException('Invalid request body.');
        }

        $tags = $this->tagRepository->findBy(['id' => $tagsIds]);
        $tagsHtml = '';

        $this->em->persist($operation);
        foreach ($tags as $tag) {
            $operation->addTag($tag);
            $this->em->persist($tag);
            $tagsHtml .= "\n".$this->twig->render('easy_admin/field_single_tag.html.twig', ['item' => $tag]);
        }
        $this->em->flush();

        $data = [
            'tags_html' => $tagsHtml,
        ];

        return new JsonResponse($data, 200);
    }
}
