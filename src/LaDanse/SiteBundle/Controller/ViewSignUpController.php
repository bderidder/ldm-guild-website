<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

/**
 * @Route("/SignUps/View")
*/
class ViewSignUpController extends LaDanseController
{
	/**
     * @Route("/", name="viewSignUpIndex")
     * @Template("LaDanseSiteBundle::viewSignUp.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
