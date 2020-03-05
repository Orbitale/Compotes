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

class YearMonthComparisonChart extends AbstractSplineChart
{
    public function getName(): string
    {
        return 'Year-month comparison';
    }

    protected function getSeries(): array
    {
        $series = [];

        foreach ($this->operations as $operation) {
            $date = $operation->getOperationDate();
            $year = $date->format('Y');
            $month = $date->format('m');

            if (!isset($series[$year])) {
                $series[$year] = [
                    'name' => $year,
                    'data' => [],
                ];
            }

            if (!isset($series[$year]['data'][$month])) {
                $series[$year]['data'][$month] = 0;
            }

            $series[$year]['data'][$month] += (int) ($operation->getAmountInCents() / 100);
        }

        foreach ($series as $year => $data) {
            $series[$year]['data'] = \array_values($data['data']);
        }

        \ksort($series);

        return \array_values($series);
    }
}
