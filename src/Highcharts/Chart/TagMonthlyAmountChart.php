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

use App\Model\JsClosure;

class TagMonthlyAmountChart extends AbstractSplineChart
{
    public function getName(): string
    {
        return 'Monthly amount per tags';
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options['plotOptions'][$options['chart']['type']]['enableMouseTracking'] = true;
        $options['plotOptions']['series']['events']['click'] = new JsClosure(
            <<<'JS'
            function() {
                this.chart.series.forEach((serie) => {serie.setVisible(false, false)});
                this.show();
            }
        JS
        );

        $options['chart']['events']['click'] = new JsClosure(
            <<<'JS'
            function () {
                this.series.forEach((serie) => {serie.setVisible(true, true)});
            }
        JS
        );

        return $options;
    }

    protected function getSeries(): array
    {
        $series = [];

        foreach ($this->operations as $operation) {
            $month = $operation->getOperationDate()->format('m');
            $tags = $operation->getTags();

            foreach ($tags as $tag) {
                $tag = $tag->getName();
                if (!isset($series[$tag])) {
                    $series[$tag] = [
                        'name' => $tag,
                        'data' => [],
                    ];
                }

                if (!isset($series[$tag]['data'][$month])) {
                    $series[$tag]['data'][$month] = null;
                }

                $amount = (int) ($operation->getAmountInCents() / 100);
                if ($amount > 0) {
                    $series[$tag]['data'][$month] += $amount;
                }
            }
        }

        foreach ($series as $tag => $data) {
            for ($i = 1; $i <= 12; $i++) {
                $y = str_pad((string) $i, 2, '0', \STR_PAD_LEFT);
                if (!isset($data['data'][$y])) {
                    $data['data'][$y] = null;
                }
            }
            ksort($data['data']);
            $series[$tag]['data'] = array_values($data['data']);
        }

        ksort($series);

        return array_values($series);
    }
}
