<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\SiteBundle\Model\ErrorModel;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordFormModel
{
    /** @var  $passwordOne string */
    private $passwordOne;

    /** @var  $passwordTwo string */
    private $passwordTwo;

    /**
     * @param string $passwordOne
     */
    public function setPasswordOne($passwordOne)
    {
        $this->passwordOne = $passwordOne;
    }

    /**
     *
     * @Assert\NotBlank()
     *
     * @return string
     */
    public function getPasswordOne()
    {
        return $this->passwordOne;
    }

    /**
     * @param string $passwordTwo
     */
    public function setPasswordTwo($passwordTwo)
    {
        $this->passwordTwo = $passwordTwo;
    }

    /**
     *
     * @Assert\NotBlank()
     *
     * @return string
     */
    public function getPasswordTwo()
    {
        return $this->passwordTwo;
    }

    public function isValid(ErrorModel $errorModel)
    {
        $isValid = true;

        if (strcmp($this->passwordOne, $this->passwordTwo) != 0)
        {
            $errorModel->addError('Both password entries must be equal');

            $isValid = false;
        }

        return $isValid;
    }
}