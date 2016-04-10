<?php

namespace LaDanse\SiteBundle\Model\Calendar;

use LaDanse\SiteBundle\Model\EventModel;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CalendarDayModel
{
    use ContainerAwareTrait;

    protected $date;
    protected $events;
    protected $showMonth;
    protected $inCurrentRaidWeek;

    public function __construct(ContainerInterface $container, \DateTime $date)
    {
        $this->setContainer($container);

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
