<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\DomainBundle\Entity\Role;

use LaDanse\SiteBundle\Model\AccountModel;

class SignUpModel extends ContainerAwareClass
{
    protected $signupType;
    protected $signedAsTank = false;
    protected $signedAsHealer = false;
    protected $signedAsDamage = false;
    protected $currentUser = false;
    protected $account;

    public function __construct(ContainerInjector $injector, SignUp $signUp)
    {
        parent::__construct($injector->getContainer());

        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        if (!is_null($account) && ($signUp->getAccount()->getId() === $account->getId()))
        {
            $this->currentUser = true;
        }

        $this->account = new AccountModel($injector, $signUp->getAccount());

        $this->signupType = $signUp->getType();

        $forRoles = $signUp->getRoles();

        foreach($forRoles as &$forRole)
        {
            switch($forRole->getRole())
            {
                case Role::TANK:
                    $this->signedAsTank = true;
                    break;
                case Role::HEALER:
                    $this->signedAsHealer = true;
                    break;
                case Role::DPS:
                    $this->signedAsDamage = true;
                    break;   
            }
        }
    }

    public function isCurrentUser()
    {
        return $this->currentUser;
    }

    public function getCurrentUserSignedUp()
    {
        return $this->getCurrentUserComes() || $this->getCurrentUserAbsent();
    }

    public function getWillCome()
    {
        return ($this->signUpType === SignupType::WILLCOME);
    }

    public function getMightCome()
    {
        return ($this->signUpType === SignupType::MIGHTCOME);
    }

    public function getAbsent()
    {
        return ($this->signUpType === SignupType::ABSENCE);
    }

    public function getSignedAsTank()
    {
        return $this->signedAsTank;
    }

    public function getSignedAsHealer()
    {
        return $this->signedAsHealer;
    }

    public function getSignedAsDamage()
    {
        return $this->signedAsDamage;
    }

    public function getAccount()
    {
        return $this->account;
    }
}
