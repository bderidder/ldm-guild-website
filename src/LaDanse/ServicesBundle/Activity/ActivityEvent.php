<?php

namespace LaDanse\ServicesBundle\Activity;

use Symfony\Component\EventDispatcher\Event;

use LaDanse\DomainBundle\Entity\Account;

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
    protected $activityOn;

    /**
     * @var Account
     */
    protected $activityBy;

    /**
     * @var array
     */
    protected $data;

    public function __construct($type, Account $usedBy, $data = null)
    {
        $this->type = $type;
        $this->activityOn = new \DateTime();
        $this->activityBy = $usedBy;
        $this->data = $data;
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
    public function getActivityOn()
    {
        return $this->activityOn;
    }

    /**
     * @param \DateTime $activityOn
     */
    public function setActivityOn($activityOn)
    {
        $this->activityOn = $activityOn;
    }

    /**
     * @return Account
     */
    public function getActivityBy()
    {
        return $this->activityBy;
    }

    /**
     * @param Account $activityBy
     */
    public function setActivityBy($activityBy)
    {
        $this->activityBy = $activityBy;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}