<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * Class EventSignUpsModel
 * @package LaDanse\SiteBundle\Model
 */
class EventSignUpsModel
{
    protected $eventId;
    protected $signUps;
    protected $organiser;
    protected $currentUserSigned;
    protected $mightComeSignUps = [];
    protected $willComeSignUps = [];
    protected $absentSignUps = [];

    protected $totalWillCome = 0;
    protected $totalMightCome = 0;

    protected $willComeTankCount = 0;
    protected $willComeHealerCount = 0;
    protected $willComeDPSCount = 0;

    protected $mightComeTankCount = 0;
    protected $mightComeHealerCount = 0;
    protected $mightComeDPSCount = 0;

    /**
     * @param DTO\Event\Event $event
     * @param Account $currentUser
     */
    public function __construct(DTO\Event\Event $event, Account $currentUser)
    {
        $this->eventId = $event->getId();

        $signUps = $event->getSignUps();

        $this->currentUserSigned = false;

        /* @var DTO\Event\SignUp $signUp */
        foreach($signUps as $signUp)
        {
            $signUpModel = new SignUpModel($signUp, $currentUser);

            if ($signUp->getAccount()->getId() === $currentUser->getId())
            {
                $this->currentUserSigned = true;
            }

            switch($signUp->getType())
            {
                case SignUpType::WILLCOME:
                    $this->totalWillCome++;
                    $this->willComeSignUps[] = $signUpModel;
                    $this->updateSignUpCounts(true, $signUpModel);
                    break;
                case SignUpType::MIGHTCOME:
                    $this->totalMightCome++;
                    $this->mightComeSignUps[] = $signUpModel;
                    $this->updateSignUpCounts(false, $signUpModel);
                    break;
                case SignUpType::ABSENCE:
                    $this->absentSignUps[] = $signUpModel;
                    break;
            }
        }
    }

    public function getId()
    {
        return $this->eventId;
    }

    public function getCurrentUserWillCome()
    {
        /* @var $signUpModel \LaDanse\SiteBundle\Model\SignUpModel */
        foreach($this->getWillComeSignUps() as $signUpModel)
        {
            if ($signUpModel->isCurrentUser())
            {
                return true;
            }
        }

        return false;
    }

    public function getCurrentUserMightCome()
    {
        /* @var $signUpModel \LaDanse\SiteBundle\Model\SignUpModel */
        foreach($this->getMightComeSignUps() as $signUpModel)
        {
            if ($signUpModel->isCurrentUser())
            {
                return true;
            }
        }

        return false;
    }

    public function getCurrentUserComes()
    {
        return $this->getCurrentUserWillCome() || $this->getCurrentUserMightCome();
    }

    public function getCurrentUserAbsent()
    {
        /* @var $signUpModel \LaDanse\SiteBundle\Model\SignUpModel */
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

    public function getTotalWillCome()
    {
        return $this->totalWillCome;
    }

    public function setTotalWillCome($totalWillCome)
    {
        $this->totalWillCome = $totalWillCome;
    }

    public function getTotalMightCome()
    {
        return $this->totalMightCome;
    }

    public function setTotalMightCome($totalMightCome)
    {
        $this->totalMightCome = $totalMightCome;
    }

    public function getWillComeTankCount()
    {
        return $this->willComeTankCount;
    }

    public function getWillComeHealerCount()
    {
        return $this->willComeHealerCount;
    }

    public function getWillComeDPSCount()
    {
        return $this->willComeDPSCount;
    }

    public function getMightComeTankCount()
    {
        return $this->mightComeTankCount;
    }

    public function getMightComeHealerCount()
    {
        return $this->mightComeHealerCount;
    }

    public function getMightComeDPSCount()
    {
        return $this->mightComeDPSCount;
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

    private function updateSignUpCounts($willCome, SignUpModel $signUpModel)
    {
        if ($signUpModel->getSignedAsTank() && $willCome)
        {
            $this->willComeTankCount++;
        }

        if ($signUpModel->getSignedAsTank() && !$willCome)
        {
            $this->mightComeTankCount++;
        }

        if ($signUpModel->getSignedAsHealer() && $willCome)
        {
            $this->willComeHealerCount++;
        }

        if ($signUpModel->getSignedAsHealer() && !$willCome)
        {
            $this->mightComeHealerCount++;
        }

        if ($signUpModel->getSignedAsDamage() && $willCome)
        {
            $this->willComeDPSCount++;
        }

        if ($signUpModel->getSignedAsDamage() && !$willCome)
        {
            $this->mightComeDPSCount++;
        }
    }
}
