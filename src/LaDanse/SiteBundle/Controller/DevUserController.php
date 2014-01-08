<?php

namespace LaDanse\SiteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use LaDanse\CommonBundle\Helper\LaDanseController;

/**
 * @Route("/devuser")
*/
class DevUserController extends LaDanseController
{
	/**
     * @Route("/login/{id}", name="loginuser")
     */
    public function login($id)
    {
		$authContext = $this->getAuthenticationService()->getCurrentContext();

    	$authContext->login($id);

        $this->addToast('Logged in as ' . $id);

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }

    /**
     * @Route("/logout", name="logoutuser")
     */
    public function logout()
    {
		$authContext = $this->getAuthenticationService()->getCurrentContext();

    	$authContext->logout();

        $this->addToast('Logged out');

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }
}
