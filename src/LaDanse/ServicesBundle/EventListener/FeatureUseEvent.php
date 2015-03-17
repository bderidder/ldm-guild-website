<?php

namespace LaDanse\ServicesBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;

use LaDanse\DomainBundle\Entity\Account;

class FeatureUseEvent extends Event
{
    const EVENT_NAME = 'LaDanse.FeatureUseEvent';

    /**
     * @var string
     */
    protected $feature;

    /**
     * @var \DateTime
     */
    protected $usedOn;

    /**
     * @var Account
     */
    protected $usedBy;

    public function __construct($feature, Account $usedBy = null)
    {
        $this->feature = $feature;
        $this->usedBy = $usedBy;
        $this->usedOn = new \DateTime();
    }

    /**
     * @return string
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param string $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return mixed
     */
    public function getUsedOn()
    {
        return $this->usedOn;
    }

    /**
     * @param mixed $usedOn
     */
    public function setUsedOn($usedOn)
    {
        $this->usedOn = $usedOn;
    }

    /**
     * @return Account
     */
    public function getUsedBy()
    {
        return $this->usedBy;
    }

    /**
     * @param Account $usedBy
     */
    public function setUsedBy(Account $usedBy)
    {
        $this->usedBy = $usedBy;
    }
}