<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommonBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class LaDanseController
 *
 * @package LaDanse\CommonBundle\Helper
 */
class LaDanseController extends Controller
{
    /**
     * @return \Symfony\Bridge\Monolog\Logger
     */
    protected function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->get('LaDanse.AuthenticationService');
    }

    /**
     * @return \LaDanse\ServicesBundle\Service\GuildCharacterService
     */
    protected function getGuildCharacterService()
    {
        return $this->get('LaDanse.GuildCharacterService');
    }

    /**
     * @return \LaDanse\ForumBundle\Service\ForumService
     */
    protected function getForumService()
    {
        return $this->get(\LaDanse\ForumBundle\Service\ForumService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\CommentBundle\Service\CommentService
     */
    protected function getCommentService()
    {
        return $this->get(\LaDanse\CommentBundle\Service\CommentService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\ServicesBundle\Service\SettingsService
     */
    protected function getSettingsService()
    {
        return $this->get(\LaDanse\ServicesBundle\Service\SettingsService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\ServicesBundle\Service\AccountService
     */
    protected function getAccountService()
    {
        return $this->get(\LaDanse\ServicesBundle\Service\AccountService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\CommonBundle\Helper\ContainerInjector
     */
    protected function getContainerInjector()
    {
        return $this->get(\LaDanse\CommonBundle\Helper\ContainerInjector::SERVICE_NAME);
    }

    /**
     * Convenience method to add a toast message to the request/session
     *
     * @param $message string
     */
    protected function addToast($message)
    {
        $toastService = $this->container->get('CoderSpotting.ToastMessage');

        $toastService->addToast($message);
    }
}
