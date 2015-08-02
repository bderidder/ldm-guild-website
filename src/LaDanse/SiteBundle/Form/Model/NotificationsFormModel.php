<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\SiteBundle\Model\ErrorModel;

class NotificationsFormModel
{
    /** @var boolean $newEvents */
    private $newEvents;

    /** @var boolean $changeSignedEvent */
    private $changeSignedEvent;

    /** @var boolean $signUpChange */
    private $signUpChange;

    /** @var boolean $topicCreated */
    private $topicCreated;

    /** @var boolean $replyToTopic */
    private $replyToTopic;

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

    /**
     * @return boolean
     */
    public function getTopicCreated()
    {
        return $this->topicCreated;
    }

    /**
     * @param boolean $topicCreated
     */
    public function setTopicCreated($topicCreated)
    {
        $this->topicCreated = $topicCreated;
    }

    /**
     * @return boolean
     */
    public function getReplyToTopic()
    {
        return $this->replyToTopic;
    }

    /**
     * @param boolean $replyToTopic
     */
    public function setReplyToTopic($replyToTopic)
    {
        $this->replyToTopic = $replyToTopic;
    }

    public function isValid(ErrorModel $errorModel)
    {
        $isValid = true;

        return $isValid;
    }
}