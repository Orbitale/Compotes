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

namespace App\Controller;

use App\Highcharts\Chart\TagUsageChart;
use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AnalyticsController
{
    private Environment $twig;
    private TagRepository $tagRepository;
    private OperationRepository $operationRepository;

    public function __construct(
        Environment $twig,
        TagRepository $tagRepository,
        OperationRepository $operationRepository
    ) {
        $this->twig = $twig;
        $this->tagRepository = $tagRepository;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @Route("/admin/analytics", name="analytics")
     */
    public function analytics(): Response
    {
        $operations = $this->operationRepository->findWithTags();

        return new Response($this->twig->render('analytics.html.twig', [
            'charts' => [
                TagUsageChart::create($operations),
            ],
        ]));
    }
}
