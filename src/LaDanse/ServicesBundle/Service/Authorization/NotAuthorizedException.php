<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

use LaDanse\ServicesBundle\Common\ServiceException;

class NotAuthorizedException extends ServiceException
{
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}