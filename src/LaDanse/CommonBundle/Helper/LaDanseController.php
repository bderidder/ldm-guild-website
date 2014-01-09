<?php

namespace LaDanse\CommonBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LaDanseController extends Controller
{
	protected function getLogger()
	{
		return $this->get('logger');
	}

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
	protected function getAuthenticationService()
	{
		return $this->get('LaDanse.AuthenticationService');
	}

    /**
     * @return \LaDanse\CommonBundle\Helper\ContainerInjector
     */
	protected function getContainerInjector()
	{
        return $this->get('LaDanse.ContainerInjector');
	}

	protected function addToast($message)
	{
		$toastService = $this->container->get('CoderSpotting.ToastMessage');

        $toastService->addToast($message);
	}
}