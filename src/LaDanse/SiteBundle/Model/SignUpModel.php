<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\DomainBundle\Entity\Role;

class SignUpModel extends ContainerAwareClass
{
    protected $signUpType;
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

        $this->signUpType = $signUp->getType();

        $forRoles = $signUp->getRoles();

        /* @var $forRole \LaDanse\DomainBundle\Entity\ForRole */
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

    /**
     * @return bool
     */
    public function isCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * @return bool
     */
    public function getCurrentUserSignedUp()
    {
        return $this->getWillCome() || $this->getMightCome() || $this->getAbsent();
    }

    /**
     * @return bool
     */
    public function getWillCome()
    {
        return ($this->signUpType === SignupType::WILLCOME);
    }

    /**
     * @return bool
     */
    public function getMightCome()
    {
        return ($this->signUpType === SignupType::MIGHTCOME);
    }

    /**
     * @return bool
     */
    public function getAbsent()
    {
        return ($this->signUpType === SignupType::ABSENCE);
    }

    /**
     * @return bool
     */
    public function getSignedAsTank()
    {
        return $this->signedAsTank;
    }

    /**
     * @return bool
     */
    public function getSignedAsHealer()
    {
        return $this->signedAsHealer;
    }

    /**
     * @return bool
     */
    public function getSignedAsDamage()
    {
        return $this->signedAsDamage;
    }

    /**
     * @return AccountModel
     */
    public function getAccount()
    {
        return $this->account;
    }
}
