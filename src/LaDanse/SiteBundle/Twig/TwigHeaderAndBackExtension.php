<?php

namespace LaDanse\SiteBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigHeaderAndBackExtension extends \Twig_Extension
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
    	$this->container = $container;
	}

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('renderHeader', array($this, 'renderFunction'), array('is_safe' => array('html'))),
        );
    }

    public function renderFunction($headerTitle, $backPath = null)
    {
        $html = $this->renderTwigTemplate($headerTitle, $backPath);

        return $html;
    }

    public function getName()
    {
        return 'LaDanseSiteBundle_HeaderAndBack_Extension';
    }

    private function renderTwigTemplate($headerTitle, $backPath)
    {
        $templating = $this->container->get('templating');

        return $templating->render('LaDanseSiteBundle:twig:PageHeader.html.twig',
            array('headerTitle' => $headerTitle,
                  'backPath' => $backPath));
    }
}