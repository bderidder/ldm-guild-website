<?php

namespace LaDanse\ForumBundle\Service;

class TopicDoesNotExistException extends \Exception
{
	public function __construct($message = null, $code = 0)
	{
        parent::__construct($message, $code);
    }
}