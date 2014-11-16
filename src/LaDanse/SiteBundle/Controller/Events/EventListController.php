<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EventListController extends LaDanseController
{
	/**
     * @Route("/", name="eventList")
     */
    public function viewAction()
    {
        return $this->render('LaDanseSiteBundle:events:eventList.html.twig');
    }
}
