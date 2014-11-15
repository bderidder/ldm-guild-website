<?php

namespace LaDanse\SiteBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

use LaDanse\SiteBundle\Model\ErrorModel;

use LaDanse\ServicesBundle\Service\AccountService;

class RegistrationFormModel
{
    /** @var  $username string */
    private $username;

    /** @var  $displayName string */
    private $displayName;

    /** @var  $email string */
    private $email;

    /** @var  $passwordOne string */
    private $passwordOne;

    /** @var  $passwordTwo string */
    private $passwordTwo;

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "4",
     *      minMessage = "Your username must be at least {{ limit }} characters long")
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "4",
     *      max = "20",
     *      minMessage = "Your display name must be at least {{ limit }} characters length",
     *      maxMessage = "Your display name cannot be longer than {{ limit }} characters length")
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPasswordOne()
    {
        return $this->passwordOne;
    }

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
    public function getPasswordTwo()
    {
        return $this->passwordTwo;
    }

    /**
     * @param string $passwordTwo
     */
    public function setPasswordTwo($passwordTwo)
    {
        $this->passwordTwo = $passwordTwo;
    }

    /**
     * @Assert\NotBlank()
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.",
     *      checkMX = false)
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function isValid(ErrorModel $errorModel,
                            FormInterface $form,
                            AccountService $accountService)
    {
        $isValid = true;

        if ($accountService->isLoginUsed($this->username))
        {
            $form->get('username')->addError(new FormError('That username is already in use by someone else'));

            $isValid = false;
        }

        if ($accountService->isDisplayNameUsed($this->displayName))
        {
            $form->get('displayName')->addError(new FormError('That display name is already in use by someone else'));

            $isValid = false;
        }

        if ($accountService->isEmailUsed($this->email))
        {
            $form->get('email')->addError(new FormError('That email address is already in use by someone else'));

            $isValid = false;
        }

        return $isValid;
    }
}