<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;

class EventModel extends ContainerAwareClass
{
    const SIGNUP_REPOSITORY = 'LaDanseDomainBundle:SignUp';

    protected $id;
    protected $name;
    protected $description;
    protected $inviteTime;
    protected $startTime;
    protected $endTime;
    protected $lastModifiedTime;
    protected $organiser;
    protected $signUpsModel;
    protected $isOrganiser;
    protected $topicId;

    public function __construct(ContainerInjector $injector, Event $event, Account $currentUser)
    {
        parent::__construct($injector->getContainer());

        $this->id = $event->getId();
        $this->name = $event->getName();
        $this->description = $event->getDescription();
        $this->inviteTime = $event->getInviteTime();
        $this->inviteTime = $event->getInviteTime();
        $this->startTime = $event->getStartTime();
        $this->endTime = $event->getEndTime();
        $this->lastModifiedTime = $event->getLastModifiedTime();
        $this->organiser = new AccountModel($injector, $event->getOrganiser());
        $this->topicId = $event->getTopicId();

        $this->signUpsModel = new EventSignUpsModel($injector, $event, $currentUser);

        $this->calculateEditable($event, $currentUser);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return AccountModel
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return EventSignUpsModel
     */
    public function getSignUps()
    {
        return $this->signUpsModel;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedTime()
    {
        return $this->lastModifiedTime;
    }

    /**
     * @return string
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * @return bool
     */
    public function getInThePast()
    {
        $now = new \DateTime('now');

        return $now > $this->inviteTime;
    }

    /**
     * @return bool
     */
    public function isOrganiser()
    {
        return $this->isOrganiser;
    }

    /**
     * @param Event $event
     * @param Account $currentUser
     */
    private function calculateEditable(Event $event, Account $currentUser)
    {
        $this->isOrganiser = false;

        if ($currentUser->getId() === $event->getOrganiser()->getId())
        {
            $this->isOrganiser = true;
        }
        else
        {
            $this->isOrganiser = false;
        }
    }
}
