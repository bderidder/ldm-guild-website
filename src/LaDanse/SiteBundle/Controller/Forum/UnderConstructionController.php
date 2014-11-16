<?php

namespace LaDanse\SiteBundle\Controller\Forum;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/", name="forumUnderConstruction")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:forum:underConstruction.html.twig");
    }
}
