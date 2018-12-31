<?php

namespace Batmaid\Calendar;

use Batmaid\Calendar\Query\TimeSlot;

class Day
{
    /**
     * @var int
     */
    private $year;
    /**
     * @var int
     */
    private $dayOfYear;
    /**
     * @var int
     */
    private $locationId;

    public function __construct(int $year, int $dayOfYear, int $locationId)
    {
        $this->year = $year;
        $this->dayOfYear = $dayOfYear;
        $this->locationId = $locationId;
    }

    public function createHourToAgentValue(TimeSlot $timeSlot, int $agentId): string
    {
        return sprintf('%s_%s', $timeSlot, $agentId);
    }

    public function getDaySlotsKey(): string
    {
        return sprintf('day_%s_%s_%s', $this->year, $this->dayOfYear, $this->locationId);
    }
}