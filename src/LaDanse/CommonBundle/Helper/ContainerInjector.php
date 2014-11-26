<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * A service wrapper to easily inject a container into a class that is not a service
 *
 * @package LaDanse\CommonBundle\Helper
 */
class ContainerInjector
{
    const SERVICE_NAME = 'LaDanse.ContainerInjector';

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
    public function getContainer()
    {
        return $this->container;
    }
}
