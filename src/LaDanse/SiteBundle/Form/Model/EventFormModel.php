<?php

namespace LaDanse\SiteBundle\Form\Model;

use DateTime;
use LaDanse\SiteBundle\Model\ErrorModel;
use Symfony\Component\Validator\Constraints as Assert;

class EventFormModel
{
    const COMPARE_DATE_FORMAT = "Y-m-d H:i";

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
     * @Assert\NotBlank()
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
    public function setInviteTime($inviteTime)
    {
    	$this->inviteTime = $inviteTime;

    	return $this;
    }

    /**
     * Get invite time
     *
     * @Assert\Time()
     * @Assert\NotBlank()
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
    public function setStartTime($startTime)
    {
    	$this->startTime = $startTime;

    	return $this;
    }

    /**
     * Get start time
     *
     * @Assert\Time()
     * @Assert\NotBlank()
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
    public function setEndTime($endTime)
    {
    	$this->endTime = $endTime;

    	return $this;
    }

    /**
     * Get end time
     *
     * @Assert\Time()
     * @Assert\NotBlank()
     *
     * @return DateTime 
     */
    public function getEndTime()
    {
    	return $this->endTime;
    }

    public function isValid(ErrorModel $errorModel)
    {
        $now = new \DateTime();
        $inviteDateTime = $this->createDateTime($this->date, $this->inviteTime);

        $isValid = true;

        if ($inviteDateTime->format(EventFormModel::COMPARE_DATE_FORMAT) 
            < $now->format(EventFormModel::COMPARE_DATE_FORMAT))
        {
            $errorModel->addError('The raid cannot be scheduled in the past');

            $isValid = false;
        }

        if ($this->inviteTime > $this->startTime)
        {
            $errorModel->addError('Invite time cannot be past start time');

            $isValid = false;
        }

        if ($this->startTime > $this->endTime)
        {
            $errorModel->addError('Start time cannot be past end time');

            $isValid = false;
        }

        return $isValid;
    }

    private function createDateTime(DateTime $datePart, DateTime $timePart)
    {
        $resultDate = new DateTime();

        $resultDate->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
        $resultDate->setTime($timePart->format('H'), $timePart->format('i'), $timePart->format('s'));

        return $resultDate;
    }
}