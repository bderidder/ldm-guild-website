<?php

namespace CoderSpotting\Bundle\ToastMessageBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\Session\Session;

class TwigToastExtension extends \Twig_Extension
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
    	$this->container = $container;
	}

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('toasts', array($this, 'toastsFunction'), array('is_safe' => array('html'))),
        );
    }

    public function toastsFunction()
    {
        $toastService = $this->container->get('CoderSpotting.ToastMessage');

        $toasts = $toastService->getToasts();

        if (!isset($toasts))
            return '';

        $html = '';

        foreach($toasts as $toastMessage)
        {
            $html .= "$().toastmessage('showNoticeToast', '" . $toastMessage . "');";
        }

        $toastService->resetToasts();

        return $html;
    }

    public function getName()
    {
        return 'CoderSpotting_ToastMessage_Extension';
    }
}