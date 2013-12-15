<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Model\AccountModel;

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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getOrganiser()
    {
        return $this->organiser;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getSignUps()
    {
        return $this->signUpsModel;
    }

    public function getLastModifiedTime()
    {
        return $this->lastModifiedTime;
    }

    public function getCurrentUserSignedUp()
    {
        return $this->currentUserSignedUp;
    }

    public function isOrganiser()
    {
        return $this->isOrganiser;
    }

    private function calculateEditable(Event $event)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $this->isOrganiser = false;

        if ($authContext->isAuthenticated() 
            && ($authContext->getAccount()->getId() === $event->getOrganiser()->getId()))
        {
            $this->isOrganiser = true;
        }
        else
        {
            $this->isOrganiser = false;   
        }
    }
}
