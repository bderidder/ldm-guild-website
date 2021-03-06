<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\ServicesBundle\Service\DTO\Event\SignUp;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpFormModel
{
	private $roles;
    private $type;

    public function __construct(SignUp $signUp = null)
    {
        if ($signUp == null)
        {
            return;
        }

        $this->type = $signUp->getType();

        $this->roles = $signUp->getRoles();
    }

	/**
     * Set roles
     *
     * @param array $roles
     * @return SignUpFormModel
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @Assert\Choice(choices = {Role::TANK, Role::HEALER, Role::DPS}, multiple = true, min = 0, max = 3)
     *
     * @return array 
     */
    public function getRoles()
    {
        // Dummy statement to trigger the proper import and avoid the import being removed
        Role::TANK;

        return $this->roles;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SignUpFormModel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @Assert\NotBlank(message = "You must select one choice")
     * @Assert\Choice(choices = {SignUpType::WILLCOME, SignUpType::MIGHTCOME, SignUpType::ABSENCE}, multiple = false)
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function isValid(FormInterface $form)
    {
        $isValid = true;

        if (($this->getType() != SignUpType::ABSENCE) && (count($this->getRoles()) == 0))
        {
            $form->get('roles')->addError(new FormError('At least one role must be chosen when not signed up as absent'));

            $isValid = false;
        }

        return $isValid;
    }
}