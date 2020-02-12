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

use App\Entity\Operation;

abstract class AbstractChart implements ChartInterface
{
    /** @var Operation[] */
    protected array $operations = [];

    abstract protected function getSeries(): array;

    abstract protected function getOptions(): array;

    /**
     * @param Operation[] $operations
     */
    public function __construct(array $operations)
    {
        foreach ($operations as $operation) {
            $this->addOperation($operation);
        }
    }

    public function getConfig(): array
    {
        return $this->getOptions() + ['series' => $this->getSeries()];
    }

    private function addOperation(Operation $operation): void
    {
        $this->operations[] = $operation;
    }
}
