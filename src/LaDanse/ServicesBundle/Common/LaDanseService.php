<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Common;

use LaDanse\SiteBundle\Security\AuthenticationService;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LaDanseService
 *
 * @package LaDanse\CommonBundle\Helper
 */
class LaDanseService
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
        return $this->container->get(AuthenticationService::SERVICE_NAME);
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    protected function get($serviceName)
    {
        return $this->container->get($serviceName);
    }
}
