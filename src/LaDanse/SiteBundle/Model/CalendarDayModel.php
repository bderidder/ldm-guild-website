<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

class CalendarDayModel extends ContainerAwareClass
{
    protected $date;
    protected $events;
    protected $showMonth;
    protected $inCurrentRaidWeek;

    public function __construct(ContainerInjector $injector, \DateTime $date)
    {
        parent::__construct($injector->getContainer());

        $this->date = $date;
        $this->events = array();
        $this->showMonth = false;
        $this->inCurrentRaidWeek = false;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function addEvent(EventModel $event)
    {
        $this->events[] = $event;
    }

    public function setShowMonth($showMonth)
    {
        $this->showMonth = $showMonth;
    }

    public function getShowMonth()
    {
        return $this->showMonth;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function hasEvents()
    {
        return count($this->events) != 0;
    }

    public function setIsInCurrentRaidWeek($inCurrentRaidWeek)
    {
        $this->inCurrentRaidWeek = $inCurrentRaidWeek;
    }

    public function isInCurrentRaidWeek()
    {
        return $this->inCurrentRaidWeek;
    }
}
