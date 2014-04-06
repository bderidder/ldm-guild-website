<?php

namespace LaDanse\SiteBundle\Controller\Events;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;

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
