<?php

namespace LaDanse\SiteBundle\Model;

class ErrorModel
{
    protected $errors;

    public function __construct()
    {
        $this->errors = [];
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
