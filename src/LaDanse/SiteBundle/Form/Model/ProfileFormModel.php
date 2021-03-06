<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\SiteBundle\Model\ErrorModel;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileFormModel
{
    /** @var  $login string */
    private $login;

    /** @var  $displayName string */
    private $displayName;

    /** @var  $email string */
    private $email;

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
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
     *      min = "3",
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
                            Account $currentAccount,
                            AccountService $accountService)
    {
        $isValid = true;

        if ($accountService->isDisplayNameUsed($this->displayName, $currentAccount->getId()))
        {
            $form->get('displayName')->addError(new FormError('That display name is already in use by someone else'));

            $isValid = false;
        }

        if ($accountService->isEmailUsed($this->email, $currentAccount->getId()))
        {
            $form->get('email')->addError(new FormError('That email address is already in use by someone else'));

            $isValid = false;
        }

        return $isValid;
    }
}