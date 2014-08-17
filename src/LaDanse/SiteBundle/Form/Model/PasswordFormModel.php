<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\SiteBundle\Model\ErrorModel;

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
     * @return string
     */
    public function getPasswordTwo()
    {
        return $this->passwordTwo;
    }

    public function isValid(ErrorModel $errorModel)
    {
        $isValid = true;

        return $isValid;
    }
}