<?php

namespace LaDanse\ServicesBundle\Common;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\SiteBundle\Security\AuthenticationContext;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractCommand
 *
 * @package LaDanse\CommonBundle\Helper
 */
abstract class AbstractCommand
{
    use ContainerAwareTrait;

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

        $this->runCommand();
    }

    /**
     * Returns true if the current request is authenticated, false otherwise
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        /** @var $authContext AuthenticationContext */
        $authContext = $this->container->get(AuthenticationService::SERVICE_NAME)->getCurrentContext();

        return $authContext->isAuthenticated();
    }

    /**
     * Returns the account that is currently logged in. When not authenticated, returns null.
     *
     * @return Account
     */
    protected function getAccount()
    {
        if ($this->isAuthenticated())
        {
            return $this->container->get(AuthenticationService::SERVICE_NAME)->getCurrentContext()->getAccount();
        }

        return null;
    }

    abstract protected function validateInput();

    abstract protected function runCommand();
}
