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

namespace App\Highcharts\Chart;

abstract class AbstractSplineChart extends AbstractChart
{
    protected function getOptions(): array
    {
        $categories = [];

        for ($i = 1; $i <= 12; $i++) {
            $categories[] = strftime('%B', (\DateTime::createFromFormat('m', (string) $i))->getTimestamp());
        }

        return [
            'chart' => [
                'type' => $type = 'spline',
                'height' => 500,
            ],
            'title' => ['text' => $this->getName()],
            'xAxis' => [
                'categories' => $categories,
            ],
            'yAxis' => [
                'title' => null,
                'plotLines' => [
                    [
                        'value' => 0,
                        'width' => 3,
                        'color' => '#000',
                    ]
                ],
            ],
            'plotOptions' => [
                $type => [
                    'dataLabels' => [
                        'enabled' => true
                    ],
                    'enableMouseTracking' => false,
                ]
            ],
        ];
    }
}
