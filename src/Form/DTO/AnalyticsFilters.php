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

namespace App\Form\DTO;

use App\Model\DateRanges;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class AnalyticsFilters
{
    /**
     * @Assert\Choice(DateRanges::RANGES)
     */
    public ?string $dateRange = null;
    /**
     * @Assert\LessThanOrEqual(propertyPath="endDate")
     */
    public ?DateTimeInterface $startDate = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="startDate")
     */
    public ?DateTimeInterface $endDate = null;

    public function updateDates(): void
    {
        $this->updateStartDate();
        $this->updateEndDate();
    }

    private function updateStartDate(): void
    {
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case DateRanges::TODAY:
                    $this->startDate = (new DateTimeImmutable())->setTime(0, 0, 0);

                    return;
                case DateRanges::THIS_WEEK:
                    // Last sunday
                    // Cannot simply use "last sunday" in date field,
                    // since "last sunday at 00:00" must return current day if current day is a sunday.
                    $this->startDate = new DateTimeImmutable('-'.\date('w').' days 00:00:00');

                    return;
                case DateRanges::LAST_WEEK:
                    // Last-last sunday
                    $this->startDate = new DateTimeImmutable('-'.((string) ((int) \date('w') + 7)).' days 00:00:00');

                    return;
                case DateRanges::THIS_MONTH:
                    $this->startDate = (new DateTimeImmutable())->setDate((int) \date('Y'), \date('m'), 1)->setTime(0, 0, 0);

                    return;
                case DateRanges::LAST_MONTH:
                    $this->startDate = (new DateTimeImmutable())->setDate((int) \date('Y'), \date('m') - 1, 1)->setTime(0, 0, 0);

                    return;
                case DateRanges::THIS_YEAR:
                    $this->startDate = (new DateTimeImmutable())->setDate((int) \date('Y'), 1, 1)->setTime(0, 0, 0);

                    return;
                case DateRanges::LAST_YEAR:
                    $this->startDate = (new DateTimeImmutable())->setDate((int) \date('Y') - 1, 1, 1)->setTime(0, 0, 0);

                    return;
                default:
                    throw new InvalidArgumentException(\sprintf('Unexpected date range "%s".', $this->dateRange));
            }
        }
    }

    private function updateEndDate(): void
    {
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case DateRanges::TODAY:
                    $this->endDate = new DateTimeImmutable('today 23:59:59');

                    return;
                case DateRanges::THIS_WEEK:
                    $this->endDate = new DateTimeImmutable('next sunday 00:00:00');

                    return;
                case DateRanges::LAST_WEEK:
                    // Last sunday (check "getStartDate" for explanations on why this code)
                    $this->endDate = new DateTimeImmutable('-'.\date('w').' days 00:00:00');

                    return;
                case DateRanges::THIS_MONTH:
                    $this->endDate = new DateTimeImmutable('last day of this month 23:59:59');

                    return;
                case DateRanges::LAST_MONTH:
                    $this->endDate = new DateTimeImmutable('last day of last month 23:59:59');

                    return;
                case DateRanges::THIS_YEAR:
                    $this->endDate = (new DateTimeImmutable())->setDate((int) \date('Y'), 12, 31)->setTime(23, 59, 59);

                    return;
                case DateRanges::LAST_YEAR:
                    $this->endDate = (new DateTimeImmutable())->setDate((int) \date('Y') - 1, 12, 31)->setTime(23, 59, 59);

                    return;
                default:
                    throw new InvalidArgumentException(\sprintf('Unexpected date range "%s".', $this->dateRange));
            }
        }
    }
}
