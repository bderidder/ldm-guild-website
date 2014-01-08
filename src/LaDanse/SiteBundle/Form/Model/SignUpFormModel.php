<?php

namespace LaDanse\SiteBundle\Form\Model;

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
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}