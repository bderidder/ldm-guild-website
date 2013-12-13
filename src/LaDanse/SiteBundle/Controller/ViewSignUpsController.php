<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

/**
 * @Route("/signups")
*/
class ViewSignUpsController extends LaDanseController
{
	/**
     * @Route("/", name="viewSignUpsIndex")
     * @Template("LaDanseSiteBundle::viewSignUps.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
