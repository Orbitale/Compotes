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

namespace App\Model;

class DateRanges
{
    public const TODAY = 'today';
    public const THIS_WEEK = 'this_week';
    public const LAST_WEEK = 'last_week';
    public const THIS_MONTH = 'this_month';
    public const LAST_MONTH = 'last_month';
    public const THIS_YEAR = 'this_year';
    public const LAST_YEAR = 'last_year';

    public const RANGES = [
        'date_ranges.'.self::TODAY => self::TODAY,
        'date_ranges.'.self::THIS_WEEK => self::THIS_WEEK,
        'date_ranges.'.self::LAST_WEEK => self::LAST_WEEK,
        'date_ranges.'.self::THIS_MONTH => self::THIS_MONTH,
        'date_ranges.'.self::LAST_MONTH => self::LAST_MONTH,
        'date_ranges.'.self::THIS_YEAR => self::THIS_YEAR,
        'date_ranges.'.self::LAST_YEAR => self::LAST_YEAR,
    ];
}
