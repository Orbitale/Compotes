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
use App\Highcharts\Chart\MonthlyBalanceChart;
use App\Highcharts\Chart\TagAmountChart;
use App\Highcharts\Chart\TagUsageChart;
use App\Highcharts\Chart\YearlyBalanceChart;
use App\Repository\OperationRepository;
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
        $form = $this->formFactory->create(AnalyticsFiltersType::class, $filters);
        $form->handleRequest($request);

        $filters->updateDates();

        $operations = $this->operationRepository->findForAnalytics($filters);

        return new Response($this->twig->render('analytics.html.twig', [
            'filters_form' => $form->createView(),
            'charts_list' => [
                'tags' => [
                    new TagUsageChart($operations),
                    new TagAmountChart($operations),
                ],
                'balance' => [
                    new YearlyBalanceChart($operations),
                    new MonthlyBalanceChart($operations),
                ],
            ],
        ]));
    }
}
