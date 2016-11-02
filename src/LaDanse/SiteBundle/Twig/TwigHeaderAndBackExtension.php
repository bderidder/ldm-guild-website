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
        return [
            new \Twig_SimpleFunction(
                'renderHeader',
                [
                    $this,
                    'renderFunction'
                ],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderFunction($headerTitle, $backPath = null)
    {
        return $this->renderTwigTemplate($headerTitle, $backPath);
    }

    public function getName()
    {
        return 'LaDanseSiteBundle_HeaderAndBack_Extension';
    }

    private function renderTwigTemplate($headerTitle, $backPath)
    {
        $templating = $this->container->get('templating');

        return $templating->render(
            'LaDanseSiteBundle:twig:PageHeader.html.twig',
            [
                'headerTitle' => $headerTitle,
                'backPath' => $backPath
            ]
        );
    }
}