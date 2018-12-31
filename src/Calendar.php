<?php

namespace Batmaid\Calendar;

use Batmaid\Calendar\Exception\CalendarDayException;
use Batmaid\Calendar\Exception\TimeSlotRangeException;
use Batmaid\Calendar\Query\TimeSlot;
use Batmaid\Calendar\Query\TimeSlotRangeFactory;
use Predis\Client;

/**
 * Whole case about
 */
class Calendar
{
    /**
     * @var Client
     */
    private $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Use this method first to create a Day context
     * @throws CalendarDayException
     */
    public function getDay(\DateTime $dayDateTime, int $locationId): Day
    {
        $diffFromToday = (new \DateTime())->diff($dayDateTime);

        if ($diffFromToday->d) {
            throw new CalendarDayException('Only today and next days are manageable');
        }

        return new Day(
            (int) $dayDateTime->format('Y'),
            (int) $dayDateTime->format('z') + 1,
            $locationId
        );
    }

    /**
     * @param Day $day
     * @param TimeSlot $from
     * @param TimeSlot $to
     * @param int $agentId
     * @throws TimeSlotRangeException
     */
    public function addHours(Day $day, TimeSlot $from, TimeSlot $to, int $agentId)
    {
        $timeSlots = TimeSlotRangeFactory::create($from, $to)->getSlots();

        $timeSlotsToAgents = $this->createTimeSlotsToAgentsRelations($timeSlots, $agentId);

        $this->redis->sadd($day->getDaySlotsKey(), $timeSlotsToAgents);
    }

    /**
     * @param Day $day
     * @param TimeSlot $from
     * @param TimeSlot $to
     * @param int $agentId
     * @throws TimeSlotRangeException
     */
    public function removeHours(Day $day, TimeSlot $from, TimeSlot $to, int $agentId)
    {
        $timeSlots = TimeSlotRangeFactory::create($from, $to)->getSlots();

        $timeSlotsToAgents = $timeSlotsToAgents = $this->createTimeSlotsToAgentsRelations($timeSlots, $agentId);

        $this->redis->srem($day->getDaySlotsKey(), $timeSlotsToAgents);
    }

    /**
     * @throws TimeSlotRangeException
     */
    public function getAvailableAgentsIds(Day $day, TimeSlot $from, TimeSlot $to): array
    {
        $requestedTimeSlotsIndexes = TimeSlotRangeFactory::create($from, $to)->getSlotIndexes();
        $numberOfRequestedTimeSlots = count($requestedTimeSlotsIndexes);

        $agentsToTimeSlotsMapping = [];
        foreach ($this->redis->smembers($day->getDaySlotsKey()) as $timeSlotIndexToAgentRelation) {
            list($timeSlotIndex, $agentId) = explode("_", $timeSlotIndexToAgentRelation);

            if (in_array($timeSlotIndex, $requestedTimeSlotsIndexes, true)) {
                $agentsToTimeSlotsMapping[$agentId][] = $timeSlotIndex;
            }
        }

        $availableAgents = [];
        foreach ($agentsToTimeSlotsMapping as $agentId => $timeSlotsIndexes) {
            $numberOfCommonTimeSlots = count(array_intersect($timeSlotsIndexes, $requestedTimeSlotsIndexes));

            if ($numberOfCommonTimeSlots === $numberOfRequestedTimeSlots) {
                $availableAgents[] = $agentId;
            }
        }

        return $availableAgents;
    }

    private function createTimeSlotsToAgentsRelations(array $timeSlots, int $agentId): array
    {
        return array_map(function(TimeSlot $timeSlot) use ($agentId) {
            return sprintf('%s_%d', $timeSlot, $agentId);
        }, $timeSlots);
    }
}