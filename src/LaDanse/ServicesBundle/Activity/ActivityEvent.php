<?php

namespace LaDanse\ServicesBundle\Activity;

use LaDanse\DomainBundle\Entity\Account;

use Symfony\Component\EventDispatcher\Event;

class ActivityEvent extends Event
{
    const EVENT_NAME = 'LaDanse.ActivityEvent';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \DateTime
     */
    protected $time;

    /**
     * @var Account
     */
    protected $actor;

    /**
     * @var mixed
     */
    protected $object;

    public function __construct($type, Account $actor = null, $object = null)
    {
        $this->type = $type;
        $this->time = new \DateTime();
        $this->actor = $actor;
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return Account
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @param Account $actor
     */
    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
}