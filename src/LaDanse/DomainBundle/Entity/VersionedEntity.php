<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\GameData\GameClass;
use LaDanse\DomainBundle\Entity\GameData\GameRace;

abstract class VersionedEntity
{
    /**
     * @ORM\Column(type="datetime", length=255, nullable=false)
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    protected $endTime;

    /**
     * Set fromTime
     *
     * @param \DateTime $fromTime
     * @return $this
     */
    public function setFromTime($fromTime)
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    /**
     * Get fromTime
     *
     * @return \DateTime 
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return $this
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Return true if the given date is within the period of this version
     *
     * @param \DateTime $onDateTime
     *
     * @return bool
     */
    public function isVersionActiveOn(\DateTime $onDateTime)
    {
        if (($this->getFromTime() <= $onDateTime)
            &&
            (($this->getEndTime() > $onDateTime) || is_null($this->getEndTime())))
        {
            return true;
        }

        return false;
    }
}
