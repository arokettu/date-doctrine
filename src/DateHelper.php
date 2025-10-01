<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;

/**
 * @internal
 */
final class DateHelper
{
    public static function parse(string $date): Date
    {
        if (class_exists(Calendar::class)) {
            return Calendar::parse($date);
        } else {
            return Date::parse($date);
        }
    }
}
