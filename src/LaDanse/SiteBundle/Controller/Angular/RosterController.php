<?php

namespace LaDanse\SiteBundle\Controller\Roster;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RosterController extends LaDanseController
{
    /**
     * @return Response
     *
     * @Route("/", name="viewRoster")
     */
    public function viewAction()
    {
        return $this->render('LaDanseSiteBundle:roster:viewRoster.html.twig');
    }
}
