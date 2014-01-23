<?php

namespace LaDanse\SiteBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use \DateTime;

class EventFormModel
{
	private $name;
	private $description;
	private $date;
	private $inviteTime;
	private $startTime;
	private $endTime;

	/**
     * Set name
     *
     * @param string $name
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @Assert\NotBlank()
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return EventFormModel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @Assert\NotBlank()
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

	/**
     * Set date
     *
     * @param DateTime $date
     * @return EventFormModel
     */
    public function setDate(DateTime $date)
    {
    	$this->date = $date;

    	return $this;
    }

	/**
     * Get date
     *
     * @Assert\Date()
     *
     * @return DateTime 
     */
    public function getDate()
    {
    	return $this->date;
    }

	/**
     * Set invite time
     *
     * @param DateTime $inviteTime
     * @return EventFormModel
     */
    public function setInviteTime(DateTime $inviteTime)
    {
    	$this->inviteTime = $inviteTime;

    	return $this;
    }

    /**
     * Get invite time
     *
     * @Assert\Time()
     *
     * @return DateTime 
     */
    public function getInviteTime()
    {
    	return $this->inviteTime;
    }

	/**
     * Set start time
     *
     * @param DateTime $startTime
     * @return EventFormModel
     */
    public function setStartTime(DateTime $startTime)
    {
    	$this->startTime = $startTime;

    	return $this;
    }

    /**
     * Get start time
     *
     * @Assert\Time()
     *
     * @return DateTime 
     */
    public function getStartTime()
    {
    	return $this->startTime;
    }

	/**
     * Set end time
     *
     * @param DateTime $endTime
     * @return EventFormModel
     */
    public function setEndTime(DateTime $endTime)
    {
    	$this->endTime = $endTime;

    	return $this;
    }

    /**
     * Get end time
     *
     * @Assert\Time()
     *
     * @return DateTime 
     */
    public function getEndTime()
    {
    	return $this->endTime;
    }
}