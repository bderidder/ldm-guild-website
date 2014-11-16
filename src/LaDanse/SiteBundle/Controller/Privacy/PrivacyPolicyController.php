<?php

namespace LaDanse\SiteBundle\Controller\Privacy;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PrivacyPolicyController extends LaDanseController
{
	/**
     * @Route("/", name="privacyPolicyIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:privacy:privacyPolicy.html.twig");
    }
}
