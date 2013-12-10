<?php

namespace LaDanse\CommonBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LaDanseController extends Controller
{
	protected function getLogger()
	{
		return $this->get('logger');
	}
}