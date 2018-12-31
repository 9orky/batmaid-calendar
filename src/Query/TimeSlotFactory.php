<?php

namespace Batmaid\Calendar\Query;

class TimeSlotFactory
{
    public static function createFromMinutesAndHours(int $hour, int $minute): TimeSlot
    {
        if ($hour < 0 || $hour > 23) {
            throw new \InvalidArgumentException('Hour value must be between 0 and 23');
        }

        if (!in_array($minute, [0, 30], true)) {
            throw new \InvalidArgumentException('Minute value must be 0 or 30');
        }

        return new TimeSlot((new \DateTime())->setTime($hour, $minute, 0));
    }

    public static function createFromTimeSlotIndex(string $timeSlotIndex): TimeSlot
    {
        if (4 !== strlen($timeSlotIndex)) {
            throw new \InvalidArgumentException('Invalid form of the TimeSlotIndex - must be HHMM');
        }

        list ($hours, $minutes) = str_split($timeSlotIndex, 2);

        return self::createFromMinutesAndHours((int) $hours, (int) $minutes);
    }

    public static function createFromDateTime(\DateTime $timeSlotDateTime): TimeSlot
    {
        return self::createFromMinutesAndHours(
            (int) $timeSlotDateTime->format('H'),
            (int) $timeSlotDateTime->format('i')
        );
    }
}