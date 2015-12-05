<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\SiteBundle\Model\ErrorModel;
use Symfony\Component\Validator\Constraints as Assert;

class CreateClaimFormModel
{
	private $character;
    private $roles;

	/**
     * Set roles
     *
     * @param array $roles
     * @return CreateClaimFormModel
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
        return $this->roles;
    }

    /**
     * Set character
     *
     * @param string $character
     * @return CreateClaimFormModel
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