<?php

namespace LaDanse\CommonBundle\Helper;

use LaDanse\SiteBundle\Security\AuthenticationService;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractQuery
 *
 * @package LaDanse\CommonBundle\Helper
 */
abstract class AbstractQuery extends ContainerAware
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param $templateName
     * @param $data
     *
     * @return string
     */
    public function renderTemplate($templateName, $data = array())
    {
        $twigEnvironment = $this->container->get('twig');

        return $twigEnvironment->render($templateName, $data);
    }

    /**
     * @param $templateName
     * @param $data
     *
     * @return string
     */
    public function createSQLFromTemplate($templateName, $data = array())
    {
        return $this->renderTemplate($templateName, $data);
    }

    public function run()
    {
        $this->validateInput();

        return $this->runQuery();
    }

    abstract protected function validateInput();

    abstract protected function runQuery();
}