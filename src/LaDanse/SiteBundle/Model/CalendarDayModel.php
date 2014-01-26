<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\SiteBundle\Model\EventModel;

class CalendarDayModel extends ContainerAwareClass
{
    protected $date;
    protected $events;

    public function __construct(ContainerInjector $injector, \DateTime $date)
    {
        parent::__construct($injector->getContainer());

        $this->date = $date;
        $this->events = array();
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

    public function getEvents()
    {
        return $this->events;
    }

    public function hasEvents()
    {
        return count($this->events) != 0;
    }
}
