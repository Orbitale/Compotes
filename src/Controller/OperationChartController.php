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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OperationChartController
{
    /**
     * @Route("/admin/operation-chart", name="operation_chart")
     */
    public function chart(Request $request)
    {
        $filters = $request->query->get('filters', []);

        return new JsonResponse([
            'filters' => $filters,
            'highcharts' => [
                'chart' => ['type' => 'bar'],
                'title' => ['text' => 'Fruit Consumption'],
                'xAxis' => [
                    'categories' => [
                        'Apples',
                        'Bananas',
                        'Oranges',
                    ],
                ],
                'yAxis' => [
                    'title' => ['text' => 'Fruit eaten'],
                ],
                'series' => [
                    [
                        'name' => 'Jane',
                        'data' => [1, 0, 4],
                    ],

                    [
                        'name' => 'John',
                        'data' => [5, 7, 3],
                    ],
                ],
            ],
        ]);
    }
}
