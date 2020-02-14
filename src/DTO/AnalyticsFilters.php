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

namespace App\DTO;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AnalyticsFilters
{
    /**
     * @Assert\LessThanOrEqual(propertyPath="endDate")
     */
    public ?DateTimeInterface $startDate = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="startDate")
     */
    public ?DateTimeInterface $endDate = null;
}
