<?php

namespace CoderSpotting\Bundle\ToastMessageBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

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
            new \Twig_SimpleFunction('renderToasts', array($this, 'toastsFunction'), array('is_safe' => array('html'))),
        );
    }

    public function toastsFunction()
    {
        $toastService = $this->container->get('CoderSpotting.ToastMessage');

        $toasts = $toastService->getToasts();

        if (!isset($toasts))
            return '';

        $html = $this->renderToastScript($toasts);

        $toastService->resetToasts();

        return $html;
    }

    public function getName()
    {
        return 'CoderSpotting_ToastMessage_Extension';
    }

    private function renderToastScript($toasts)
    {
        $templating = $this->container->get('templating');

        return $templating->render('CoderSpottingToastMessageBundle::javascript.html.twig',
            array('toasts' => $toasts));
    }
}