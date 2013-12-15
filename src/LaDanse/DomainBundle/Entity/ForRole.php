<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use LaDanse\DomainBundle\Entity\SignUp;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForRole")
 */
class ForRole
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="SignUp", inversedBy="roles")
     * @ORM\JoinColumn(name="signUpId", referencedColumnName="id", nullable=false)
     */
    protected $signUp;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return ForRole
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set signUp
     *
     * @param \LaDanse\DomainBundle\Entity\SignUp $signUp
     * @return ForRole
     */
    public function setSignUp(\LaDanse\DomainBundle\Entity\SignUp $signUp = null)
    {
        $this->signUp = $signUp;

        return $this;
    }

    /**
     * Get signUp
     *
     * @return \LaDanse\DomainBundle\Entity\SignUp 
     */
    public function getSignUp()
    {
        return $this->signUp;
    }
}
