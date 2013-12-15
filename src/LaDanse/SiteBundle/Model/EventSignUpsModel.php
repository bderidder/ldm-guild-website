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
    protected $currentUserSignedUp;

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

        $this->currentUserSignedUp = false;

        foreach($signUps as &$signUp)
        {
            if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
            {
                $this->currentUserSignedUp = true; 
            }

            switch($signUp->getType())
            {
                case SignUpType::WILLCOME:
                    $this->willComeCount = $this->willComeCount + 1;
                    break;
                case SignUpType::MIGHTCOME:
                    $this->mightComeCount = $this->mightComeCount + 1;
                    break;
                case SignUpType::ABSENCE:
                    $this->absentCount = $this->absentCount + 1;
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
        return $this->currentUserSignedUp;
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

        /*
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery('SELECT s FROM LaDanse\DomainBundle\Entity\SignUp s WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $account->getId());
        $query->setParameter('event', $this->getId());
        
        $signUps = $query->getResult();

        if(!is_null($signUps) && is_array($signUps) && count($signUps) > 0)
        {
            $this->currentUserSignedUp = true;   
        }
        else
        {
            $this->currentUserSignedUp = false;
        }
        */
