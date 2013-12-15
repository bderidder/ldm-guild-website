<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Model\AccountModel;

class EventSignUpsModel extends ContainerAwareClass
{
    const SIGNUP_REPOSITORY = 'LaDanseDomainBundle:SignUp';

    protected $eventId;
    protected $signUps;
    protected $signupCount;
    protected $organiser;
    protected $currentUserSignedUp;

    public function __construct(ContainerInjector $injector, Event $event)
    {
        parent::__construct($injector->getContainer());

        $this->eventId = $event->getId();

        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $signUps = $event->getSignUps();

        $this->signupCount = count($signUps);

        $this->currentUserSignedUp = false;

        foreach($signUps as &$signUp)
        {
            if ($signUp->getAccount()->getId() === $account->getId())
            {
                $this->currentUserSignedUp = true; 
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

    public function getSignUpCount()
    {
        return $this->signupCount;
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
