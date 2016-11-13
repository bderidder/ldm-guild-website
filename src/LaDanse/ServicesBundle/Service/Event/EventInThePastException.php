<?php

namespace LaDanse\ServicesBundle\Service\Event;

use LaDanse\ServicesBundle\Common\ServiceException;

class EventInThePastException extends ServiceException
{
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}