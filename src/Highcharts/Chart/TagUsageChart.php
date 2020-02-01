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

class TagUsageChart extends AbstractChart
{
    /** * @var Operation[] */
    private array $operations = [];

    private function __construct()
    {
    }

    public function getName(): string
    {
        return 'Tags';
    }

    public static function create($operations): self
    {
        $self = new self();

        foreach ($operations as $operation) {
            $self->addOperation($operation);
        }

        return $self;
    }

    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => $type = 'bar',
                'height' => 500,
            ],
            'legend' => [
                'align' => 'right',
                'layout' => 'vertical',
            ],
            'title' => ['text' => 'Tags usage'],
            'xAxis' => [
                'categories' => ['Tags'],
            ],
            'yAxis' => [
                'title' => ['text' => 'Number of operations with these tags'],
            ],
            'plotOptions' => [
                $type => [
                    'pointWidth' => 10,
                    'borderWidth' => 0,
                    'groupPadding' => 0.01,
                ],
            ],
        ];
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

                $series[$tagName]['data'][0]++;
            }
        }

        \ksort($series);

        return \array_values($series);
    }

    private function addOperation(Operation $operation): void
    {
        $this->operations[] = $operation;
    }
}
