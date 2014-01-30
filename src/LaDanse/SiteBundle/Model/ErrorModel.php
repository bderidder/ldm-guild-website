<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Account;

class ErrorModel
{
    protected $errors;

    public function __construct()
    {
        $this->errors = array();
    }

    public function getHasErrors()
    {
        return count($this->errors) != 0;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
