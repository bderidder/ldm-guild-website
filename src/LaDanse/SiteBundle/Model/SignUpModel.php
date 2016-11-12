<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\ServicesBundle\Service\DTO as DTO;

class SignUpModel
{
    protected $signUpType;
    protected $signedAsTank = false;
    protected $signedAsHealer = false;
    protected $signedAsDamage = false;
    protected $currentUser = false;
    protected $account;

    public function __construct(DTO\Event\SignUp $signUp, Account $currentUser)
    {
        if ($signUp->getAccount()->getId() === $currentUser->getId())
        {
            $this->currentUser = true;
        }

        $this->account = new AccountModel($signUp->getAccount());

        $this->signUpType = $signUp->getType();

        $forRoles = $signUp->getRoles();

        /* @var string $forRole */
        foreach($forRoles as &$forRole)
        {
            switch($forRole)
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
        return ($this->signUpType === SignUpType::WILLCOME);
    }

    /**
     * @return bool
     */
    public function getMightCome()
    {
        return ($this->signUpType === SignUpType::MIGHTCOME);
    }

    /**
     * @return bool
     */
    public function getAbsent()
    {
        return ($this->signUpType === SignUpType::ABSENCE);
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
