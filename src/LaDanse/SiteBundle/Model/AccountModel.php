<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Account;

class AccountModel extends ContainerAwareClass
{
    protected $id;
    protected $name;

    public function __construct(ContainerInjector $injector, Account $account)
    {
        parent::__construct($injector->getContainer());
    
        $this->id = $account->getId();
        $this->name = '';
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
