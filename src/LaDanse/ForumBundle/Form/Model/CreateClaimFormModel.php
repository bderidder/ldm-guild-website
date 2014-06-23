<?php

namespace LaDanse\SiteBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use LaDanse\SiteBundle\Model\ErrorModel;

use LaDanse\DomainBundle\Entity\Role;

class CreateClaimFormModel
{
	private $character;
    private $roles;

	/**
     * Set roles
     *
     * @param array $roles
     * @return NewClaimFormModel
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @Assert\Choice(choices = {Role::TANK, Role::HEALER, Role::DPS}, multiple = true, min = 1, max = 3)
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set character
     *
     * @param string $character
     * @return NewClaimFormModel
     */
    public function setCharacter($character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @Assert\NotBlank(message = "You must select a character")
     *
     * @return string 
     */
    public function getCharacter()
    {
        return $this->character;
    }

    public function isValid(ErrorModel $errorModel)
    {
        return true;
    }
}