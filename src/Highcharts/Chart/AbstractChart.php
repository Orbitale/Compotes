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

abstract class AbstractChart implements ChartInterface
{
    public function getConfig(): array
    {
        return $this->getOptions() + ['series' => $this->getSeries()];
    }

    abstract protected function getSeries(): array;

    abstract protected function getOptions(): array;
}
