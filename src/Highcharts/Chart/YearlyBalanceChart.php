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

class YearlyBalanceChart extends AbstractBarChart
{
    public function getName(): string
    {
        return 'Yearly balance';
    }

    protected function getSeries(): array
    {
        $series = [];

        foreach ($this->operations as $operation) {
            $year = $operation->getOperationDate()->format('Y');
            if (!isset($series[$year])) {
                $series[$year] = [
                    'name' => $year,
                    'data' => [0],
                ];
            }
            $series[$year]['data'][0] += (int) ($operation->getAmountInCents() / 100);
        }

        ksort($series);

        return array_values($series);
    }
}
