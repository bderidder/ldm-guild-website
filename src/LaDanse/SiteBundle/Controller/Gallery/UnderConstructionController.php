<?php

namespace LaDanse\SiteBundle\Controller\Gallery;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/", name="galleryUnderConstruction")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:gallery:underConstruction.html.twig");
    }
}
