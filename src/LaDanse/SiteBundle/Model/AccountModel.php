<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Account;

class AccountModel extends ContainerAwareClass
{
    protected $id;
    protected $name;
    protected $displayName;

    public function __construct(ContainerInjector $injector, Account $account)
    {
        parent::__construct($injector->getContainer());
    
        $this->id = $account->getId();
        $this->name = $account->getUsername();
        $this->displayName = $account->getDisplayName();
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
     * @param string $name
     * @return AccountModel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
