<?php

namespace LaDanse\SiteBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;

class SignUpFormModel
{
	private $roles;
    private $type;

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
     * @Assert\Choice(choices = {Role::TANK, Role::HEALER, Role::DPS}, multiple = true, min = 1, max = 3)
     *
     * @return array 
     */
    public function getRoles()
    {
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
     * @Assert\Choice(choices = {SignUpType::WILLCOME, SignUpType::MIGHTCOME}, multiple = false)
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}