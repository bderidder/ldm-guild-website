<?php

namespace LaDanse\SiteBundle\Twig;

use LaDanse\ServicesBundle\FeatureToggle\FeatureToggleService;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigFeatureToggleExtension extends \Twig_Extension
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
    	$this->container = $container;
	}

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hasFeatureToggle', array($this, 'hasFeatureToggle'), array('is_safe' => array('html'))),
        );
    }

    public function hasFeatureToggle($featureToggle)
    {
        /** @var AuthenticationService $authService */
        $authService = $this->container->get(AuthenticationService::SERVICE_NAME);

        /** @var FeatureToggleService $featureToggleService */
        $featureToggleService = $this->container->get(FeatureToggleService::SERVICE_NAME);

        return $featureToggleService->hasAccountFeatureToggled(
            $authService->getCurrentContext()->getAccount(),
            $featureToggle,
            false);
    }

    public function getName()
    {
        return 'LaDanseSiteBundle_FeatureToggle_Extension';
    }
}