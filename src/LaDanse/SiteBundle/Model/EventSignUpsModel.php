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
    protected $currentUserSigned;
    protected $mightComeSignUps = array();
    protected $willComeSignUps = array();
    protected $absentSignUps = array();

    public function __construct(ContainerInjector $injector, Event $event)
    {
        parent::__construct($injector->getContainer());

        $this->eventId = $event->getId();

        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $signUps = $event->getSignUps();

        $this->currentUserSigned = false;

        foreach($signUps as &$signUp)
        {
            $signUpModel = new SignUpModel($injector, $signUp);

            if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
            {
                $this->currentUserSigned = true;
            }

            switch($signUp->getType())
            {
                case SignUpType::WILLCOME:
                    $this->willComeSignUps[] = $signUpModel;
                    break;
                case SignUpType::MIGHTCOME:
                    $this->mightComeSignUps[] = $signUpModel;
                    break;
                case SignUpType::ABSENCE:
                    $this->absentSignUps[] = $signUpModel;
                    break;   
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCurrentUserComes()
    {
        foreach($this->getWillComeSignUps() as $signUpModel)
        {
            if ($signUpModel->isCurrentUser())
            {
                return true;
            }
        }

        foreach($this->getMightComeSignUps() as $signUpModel)
        {
            if ($signUpModel->isCurrentUser())
            {
                return true;
            }
        }

        return false;
    }

    public function getCurrentUserAbsent()
    {
        foreach($this->getAbsentSignUps() as $signUpModel)
        {
            if ($signUpModel->isCurrentUser())
            {
                return true;
            }
        }

        return false;
    }

    public function getCurrentUserSignedUp()
    {
        return $this->currentUserSigned;
    }

    public function getWillComeSignUps()
    {
        return $this->willComeSignUps;
    }

    public function getMightComeSignUps()
    {
        return $this->mightComeSignUps;
    }

    public function getAbsentSignUps()
    {
        return $this->absentSignUps;
    }

    public function getWillComeCount()
    {
        return count($this->willComeSignUps);
    }

    public function getMightComeCount()
    {
        return count($this->mightComeSignUps);
    }

    public function getAbsentCount()
    {
        return count($this->absentSignUps);
    }

    public function getSignUpCount()
    {
        return $this->getWillComeCount() + $this->getMightComeCount();
    }
}
