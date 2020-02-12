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

use App\DTO\AnalyticsFilters;
use App\Form\Type\AnalyticsFiltersType;
use App\Highcharts\Chart\TagAmountChart;
use App\Highcharts\Chart\TagUsageChart;
use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AnalyticsController
{
    private Environment $twig;
    private FormFactoryInterface $formFactory;
    private OperationRepository $operationRepository;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        TagRepository $tagRepository,
        OperationRepository $operationRepository
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @Route("/admin/analytics", name="analytics", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $filters = new AnalyticsFilters();
        $form = $this->formFactory->createNamed('', AnalyticsFiltersType::class, $filters);
        $form->handleRequest($request);

        $operations = $this->operationRepository->findForAnalytics($filters);

        return new Response($this->twig->render('analytics.html.twig', [
            'filters_form' => $form->createView(),
            'charts' => [
                new TagUsageChart($operations),
                new TagAmountChart($operations),
            ],
        ]));
    }
}
