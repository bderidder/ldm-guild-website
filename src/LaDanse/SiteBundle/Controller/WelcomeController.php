<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LaDanse\SiteBundle\Security\AuthenticationContext;

class WelcomeController extends Controller
{
    public function indexAction(Request $request)
    {
    	$authContext = new AuthenticationContext($this->get('LaDanse.ContainerInjector'), $request);

        return $this->render('LaDanseSiteBundle::index.html.twig');
    }
}
