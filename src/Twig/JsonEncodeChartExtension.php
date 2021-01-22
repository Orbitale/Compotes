<?php

namespace App\Twig;

use App\Highcharts\Chart\ChartInterface;
use App\Model\JsClosure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonEncodeChartExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('json_encode_chart', [$this, 'jsonEncodeChart'], ['is_safe' => ['html']]),
        ];
    }

    public function jsonEncodeChart(ChartInterface $chart): string
    {
        $options = $chart->getConfig();

        $encoded = \json_encode($options, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_LINE_TERMINATORS);

        $closureTagsRegex = \sprintf('~"%s(.+)%s"~isU', JsClosure::TAG_START, JsClosure::TAG_END);
        $encoded = \preg_replace_callback($closureTagsRegex, static function (array $matches) {
            return \str_replace('\n', "\n", \trim($matches[1]));
        }, $encoded);

        return $encoded;
    }
}