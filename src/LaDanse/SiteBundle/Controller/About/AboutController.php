<?php

namespace LaDanse\SiteBundle\Controller\About;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AboutController extends LaDanseController
{
	/**
     * @return Response
     *
     * @Route("/", name="aboutIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:about:about.html.twig");
    }
}
