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

class TagAmountChart extends AbstractBarChart
{
    public function getName(): string
    {
        return 'Tags amount';
    }

    protected function getSeries(): array
    {
        $series = [];

        foreach ($this->operations as $operation) {
            foreach ($operation->getTags() as $tag) {
                $tagName = $tag->getName();
                if (!isset($series[$tagName])) {
                    $series[$tagName] = [
                        'name' => $tagName,
                        'data' => [0],
                    ];
                }

                $series[$tagName]['data'][0] += (int) ($operation->getAmountInCents() / 100);
            }
        }

        \ksort($series);

        return \array_values($series);
    }
}
