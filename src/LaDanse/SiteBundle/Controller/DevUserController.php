<?php

namespace LaDanse\SiteBundle\Controller;

use \DateTime;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use LaDanse\SiteBundle\Form\Model\NewEventFormModel;
use LaDanse\SiteBundle\Form\Type\NewEventFormType;

/**
 * @Route("/devuser")
*/
class DevUserController extends LaDanseController
{
	/**
     * @Route("/login/{id}", name="loginuser")
     */
    public function login(Request $request, $id)
    {
		$authContext = $this->getAuthenticationService()->getCurrentContext();

    	$authContext->login($id);

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }

    /**
     * @Route("/logout", name="logoutuser")
     */
    public function logout(Request $request)
    {
		$authContext = $this->getAuthenticationService()->getCurrentContext();

    	$authContext->logout();

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }
}
