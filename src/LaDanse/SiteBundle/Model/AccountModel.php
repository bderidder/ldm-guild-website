<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class AccountModel
{
    protected $id;
    protected $displayName;

    public function __construct(AccountReference $account)
    {
        $this->id = $account->getId();
        $this->displayName = $account->getName();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $displayName
     * @return AccountModel
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
}
