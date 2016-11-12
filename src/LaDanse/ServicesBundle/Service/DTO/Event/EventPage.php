<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

class EventPage
{
    /** @var array */
    private $events;

    /** @var \DateTime */
    private $previousTimestamp;

    /** @var \DateTime */
    private $nextTimestamp;

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array $events
     * @return EventPage
     */
    public function setEvents(array $events): EventPage
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPreviousTimestamp(): \DateTime
    {
        return $this->previousTimestamp;
    }

    /**
     * @param \DateTime $previousTimestamp
     * @return EventPage
     */
    public function setPreviousTimestamp(\DateTime $previousTimestamp): EventPage
    {
        $this->previousTimestamp = $previousTimestamp;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNextTimestamp(): \DateTime
    {
        return $this->nextTimestamp;
    }

    /**
     * @param \DateTime $nextTimestamp
     * @return EventPage
     */
    public function setNextTimestamp(\DateTime $nextTimestamp): EventPage
    {
        $this->nextTimestamp = $nextTimestamp;
        return $this;
    }
}