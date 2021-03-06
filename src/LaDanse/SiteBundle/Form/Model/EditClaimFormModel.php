<?php

namespace LaDanse\SiteBundle\Form\Model;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\SiteBundle\Model\ErrorModel;
use Symfony\Component\Validator\Constraints as Assert;

class EditClaimFormModel
{
	private $roles;

    public function __construct($claimModel)
    {
        $this->roles = [];

        if ($claimModel->playsTank)
        {
            $this->roles[] = Role::TANK;
        }

        if ($claimModel->playsHealer)
        {
            $this->roles[] = Role::HEALER;
        }

        if ($claimModel->playsDPS)
        {
            $this->roles[] = Role::DPS;
        }
    }

	/**
     * Set roles
     *
     * @param array $roles
     * @return EditClaimFormModel
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

    public function isValid(ErrorModel $errorModel)
    {
        return true;
    }
}