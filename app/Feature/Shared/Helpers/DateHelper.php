<?php

namespace App\Feature\Shared\Helpers;

use DateTime;

/**
 * Class DateHelper
 *
 * A helper class for date formatting.
 *
 * @package App\Feature\Shared\Helpers
 */
class DateHelper
{
    /**
     * Set the start time for a given date to the start of the day (00:00:00).
     *
     * @param string $date
     * @return string
     */
    public static function setStartTime(string $date): string
    {
        return (new DateTime($date))->format('Y-m-d 00:00:00');
    }

    /**
     * Set the end time for a given date to the end of the day (23:59:59).
     *
     * @param string $date
     * @return string
     */
    public static function setEndTime(string $date): string
    {
        return (new DateTime($date))->format('Y-m-d 23:59:59');
    }
}
