<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

use LaDanse\DomainBundle\Entity\Account;

class SubjectReference
{
    /** @var Account $account */
    private $account;

    /**
     * SubjectReference constructor.
     *
     * @param Account $account
     */
    public function __construct(Account $account = null)
    {
        $this->account = $account;
    }

    /**
     * @return bool
     */
    public function isAnonymous()
    {
        return ($this->account == null);
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }
}