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
     * @var int
     * @SerializedName("id")
     */
    private $claimId;

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
     * @return int
     */
    public function getClaimId(): int
    {
        return $this->claimId;
    }

    /**
     * @param int $claimId
     * @return Claim
     */
    public function setClaimId(int $claimId): Claim
    {
        $this->claimId = $claimId;
        return $this;
    }

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
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Claim
     */
    public function setComment(string $comment): Claim
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