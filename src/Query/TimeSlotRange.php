<?php

namespace Batmaid\Calendar\Query;

class TimeSlotRange
{
    const TIME_SLOT_MINUTES = 30;

    private $beginFrom;
    private $terminateAt;

    public function __construct(TimeSlot $beginFrom, TimeSlot $terminateAt)
    {
        $this->beginFrom = $beginFrom;
        $this->terminateAt = $terminateAt;
    }

    public function getSlots(): array
    {
        $currentTimeSlot = $this->beginFrom;
        $terminatingTimeSlot = $this->terminateAt;

        $timeSlots = [$currentTimeSlot];
        while ($terminatingTimeSlot->isLaterThan($currentTimeSlot)) {
            $nextSlotInterval = new \DateInterval(sprintf('PT%dM', self::TIME_SLOT_MINUTES));
            $nextSlotDateTime = (clone $currentTimeSlot->getDateTime())->add($nextSlotInterval);

            $currentTimeSlot = TimeSlotFactory::createFromDateTime($nextSlotDateTime);
            $timeSlots[] = $currentTimeSlot;
        }

        return $timeSlots;
    }

    public function getSlotIndexes(): array
    {
        return array_map(function(TimeSlot $timeSlot) {
            return (string) $timeSlot;
        }, $this->getSlots());
    }
}