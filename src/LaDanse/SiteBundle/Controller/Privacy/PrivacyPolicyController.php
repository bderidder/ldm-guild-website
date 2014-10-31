<?php

namespace LaDanse\SiteBundle\Controller\Privacy;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class PrivacyPolicyController extends LaDanseController
{
	/**
     * @Route("/", name="privacyPolicyIndex")
     * @Template("LaDanseSiteBundle:privacy:privacyPolicy.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
