<?php

namespace CoderSpotting\Bundle\ToastMessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CoderSpottingToastMessageBundle:Default:index.html.twig', array('name' => $name));
    }
}
