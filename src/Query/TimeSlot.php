<?php

namespace Batmaid\Calendar\Query;

class TimeSlot
{
    /**
     * @var \DateTime
     */
    private $dateTime;

    public function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function isLaterThan(self $compareTo): bool
    {
        return $this->dateTime > $compareTo->getDateTime();
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function __toString()
    {
        return $this->dateTime->format('Hi');
    }
}