<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\SignUpType;

use LaDanse\SiteBundle\Model\AccountModel;

class EventSignUpsModel extends ContainerAwareClass
{
    const SIGNUP_REPOSITORY = 'LaDanseDomainBundle:SignUp';

    protected $eventId;
    protected $signUps;    
    protected $willComeCount;
    protected $mightComeCount;
    protected $absentCount;
    protected $organiser;
    protected $currentUserComes;
    protected $currentUserAbsent;

    public function __construct(ContainerInjector $injector, Event $event)
    {
        parent::__construct($injector->getContainer());

        $this->eventId = $event->getId();

        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $signUps = $event->getSignUps();

        $this->willComeCount = 0;
        $this->mightComeCount = 0;
        $this->absentCount = 0;

        $this->currentUserComes = false;
        $this->currentUserAbsent = false;

        foreach($signUps as &$signUp)
        {
            switch($signUp->getType())
            {
                case SignUpType::WILLCOME:
                    $this->willComeCount = $this->willComeCount + 1;

                    if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
                    {
                        $this->currentUserComes = true;
                    }

                    break;
                case SignUpType::MIGHTCOME:
                    $this->mightComeCount = $this->mightComeCount + 1;

                    if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
                    {
                        $this->currentUserComes = true;
                    }

                    break;
                case SignUpType::ABSENCE:
                    $this->absentCount = $this->absentCount + 1;

                    if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
                    {
                        $this->currentUserAbsent = true;
                    }

                    break;   
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCurrentUserSignedUp()
    {
        return $this->getCurrentUserComes() || $this->getCurrentUserAbsent();
    }

    public function getCurrentUserComes()
    {
        return $this->currentUserComes;
    }

    public function getCurrentUserAbsent()
    {
        return $this->currentUserAbsent;
    }

    public function getEditable()
    {
        return $this->editable;
    }

    public function getWillComeCount()
    {
        return $this->willComeCount;
    }

    public function getMightComeCount()
    {
        return $this->mightComeCount;
    }

    public function getAbsentCount()
    {
        return $this->absentCount;
    }

    public function getSignUpCount()
    {
        return $this->getWillComeCount() + $this->getMightComeCount();
    }
}
