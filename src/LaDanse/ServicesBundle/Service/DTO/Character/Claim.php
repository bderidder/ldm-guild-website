<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class Claim
{
    /** @var int */
    private $claimId;

    /** @var AccountReference */
    private $accountReference;

    /** @var array */
    private $roles;

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
}