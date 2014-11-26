<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LaDanseService
 *
 * @package LaDanse\CommonBundle\Helper
 */
class LaDanseService extends ContainerAware
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
     * @return string
     */
    public function renderTemplate($templateName)
    {
        $twigEnvironment = $this->container->get('twig');

        return $twigEnvironment->render($templateName);
    }

    /**
     * @param $templateName
     * @return string
     */
    public function createSQLFromTemplate($templateName)
    {
        return $this->renderTemplate($templateName);
    }

    /**
     * @return \Symfony\Bridge\Monolog\Logger
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->container->get('LaDanse.AuthenticationService');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \LaDanse\CommonBundle\Helper\ContainerInjector
     */
    protected function getContainerInjector()
    {
        return $this->container->get('LaDanse.ContainerInjector');
    }
}
