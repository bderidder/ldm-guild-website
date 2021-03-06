<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

/**
 * @ExclusionPolicy("none")
 */
class Claim
{
    /**
     * @var AccountReference
     * @SerializedName("accountReference")
     */
    private $accountReference;

    /**
     * @var array
     * @SerializedName("roles")
     */
    private $roles;

    /**
     * @var string
     * @SerializedName("comment")
     */
    protected $comment;

    /**
     * @var bool
     * @SerializedName("raider")
     */
    protected $raider = false;

    /**
     * @return AccountReference
     */
    public function getAccountReference(): AccountReference
    {
        return $this->accountReference;
    }

    /**
     * @param AccountReference $accountReference
     * @return Claim
     */
    public function setAccountReference(AccountReference $accountReference): Claim
    {
        $this->accountReference = $accountReference;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return Claim
     */
    public function setRoles(array $roles): Claim
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $playsRole
     * @return bool
     */
    public function playsRole($playsRole) : bool
    {
        foreach($this->roles as $role)
        {
            if ($role == $playsRole)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Claim
     */
    public function setComment($comment): Claim
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRaider(): bool
    {
        return $this->raider;
    }

    /**
     * @param boolean $raider
     * @return Claim
     */
    public function setRaider(bool $raider): Claim
    {
        $this->raider = $raider;
        return $this;
    }
}