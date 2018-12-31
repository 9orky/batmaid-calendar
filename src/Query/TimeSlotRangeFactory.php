<?php

namespace Batmaid\Calendar\Query;

use Batmaid\Calendar\Exception\TimeSlotRangeException;

class TimeSlotRangeFactory
{
    /**
     * @throws TimeSlotRangeException
     */
    public static function create(TimeSlot $from, TimeSlot $to): TimeSlotRange
    {
        if ($from->isLaterThan($to)) {
            throw new TimeSlotRangeException('Querying the past or day overflow');
        }

        return new TimeSlotRange($from, $to);
    }
}