<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\SiteBundle\Model\ErrorModel;

class NotificationsFormModel
{
    /** @var  $newEvents boolean */
    private $newEvents;

    /** @var  $changeSignedEvent boolean */
    private $changeSignedEvent;

    /** @var  $signUpChange boolean */
    private $signUpChange;

    /**
     * @param boolean $changeSignedEvent
     */
    public function setChangeSignedEvent($changeSignedEvent)
    {
        $this->changeSignedEvent = $changeSignedEvent;
    }

    /**
     * @return boolean
     */
    public function getChangeSignedEvent()
    {
        return $this->changeSignedEvent;
    }

    /**
     * @param boolean $newEvents
     */
    public function setNewEvents($newEvents)
    {
        $this->newEvents = $newEvents;
    }

    /**
     * @return boolean
     */
    public function getNewEvents()
    {
        return $this->newEvents;
    }

    /**
     * @param boolean $signUpChange
     */
    public function setSignUpChange($signUpChange)
    {
        $this->signUpChange = $signUpChange;
    }

    /**
     * @return boolean
     */
    public function getSignUpChange()
    {
        return $this->signUpChange;
    }

    public function isValid(ErrorModel $errorModel)
    {
        $isValid = true;

        return $isValid;
    }
}