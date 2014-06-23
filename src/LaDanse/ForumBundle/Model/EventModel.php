<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

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
    protected $signUpsModel;
    protected $isOrganiser;

    public function __construct(ContainerInjector $injector, Event $event)
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

        $this->signUpsModel = new EventSignUpsModel($injector, $event);

        $this->calculateEditable($event);
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

    private function calculateEditable(Event $event)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $this->isOrganiser = FALSE;

        if ($authContext->isAuthenticated() 
            && ($authContext->getAccount()->getId() === $event->getOrganiser()->getId()))
        {
            $this->isOrganiser = TRUE;
        }
        else
        {
            $this->isOrganiser = FALSE;
        }
    }
}
