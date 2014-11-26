<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Class ContainerAwareClass
 *
 * @package LaDanse\CommonBundle\Helper
 */
class ContainerAwareClass
{
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $id
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return object
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return object
     */
    protected function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->get('LaDanse.AuthenticationService');
    }
}
