<?php

namespace LaDanse\SiteBundle\Controller\Help;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HelpController extends LaDanseController
{
	/**
     * @Route("/", name="helpIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:help:index.html.twig");
    }
}
