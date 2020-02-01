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

interface ChartInterface
{
    public function getName(): string;

    /**
     * This corresponds to the options sent to the Highcharts js object.
     *
     * Config has to be convertible to JSON or JS.
     * If your config contains closures, it will be rendered as a string (for now).
     *
     * @see https://api.highcharts.com/highcharts/
     */
    public function getConfig(): array;
}
