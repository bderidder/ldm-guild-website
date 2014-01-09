<?php

namespace CoderSpotting\Bundle\ToastMessageBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

class ToastMessageService extends ContainerAware
{
	public function __construct(ContainerInterface $container)
	{
		$this->setContainer($container);
	}

	public function addToast($message)
	{
		$session = $this->container->get('session');

		$toasts = $session->get('CoderSpotting_ToastMessages');

        if (!isset($toasts))
        {
        	$toasts = array();
        }

		$toasts[] = $message;

		$session->set('CoderSpotting_ToastMessages', $toasts);

		$session->save();

		return $this;
	}

	public function getToasts()
	{
		$session = $this->container->get('session');

		$toasts = $session->get('CoderSpotting_ToastMessages');

        if (!isset($toasts))
        {
        	$toasts = array();
        }

        return $toasts;
	}

	public function resetToasts()
	{
		$session = $this->container->get('session');

		$session->remove('CoderSpotting_ToastMessages');

		return $this;
	}
}