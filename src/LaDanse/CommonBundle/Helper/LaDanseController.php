<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommonBundle\Helper;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\CannotEvaluateException;
use LaDanse\ServicesBundle\Service\Authorization\ResourceReference;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\FeatureToggle\FeatureToggleService;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\SiteBundle\Security\AuthenticationService;
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
     * Returns true if the current request is authenticated, false otherwise
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

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
            return $this->getAuthenticationService()->getCurrentContext()->getAccount();
        }

        return null;
    }

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->get(AuthenticationService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\ServicesBundle\Service\Account\AccountService
     */
    protected function getAccountService()
    {
        return $this->get(AccountService::SERVICE_NAME);
    }

    /**
     * @return \LaDanse\CommonBundle\Helper\ContainerInjector
     */
    protected function getContainerInjector()
    {
        return $this->get(ContainerInjector::SERVICE_NAME);
    }

    /**
     * @param SubjectReference $subject
     * @param string $action
     * @param ResourceReference $resource
     *
     * @return bool
     *
     * @throws CannotEvaluateException
     */
    protected function isAuthorized(SubjectReference $subject, $action, ResourceReference $resource)
    {
        /** @var AuthorizationService $authzService */
        $authzService = $this->get(AuthorizationService::SERVICE_NAME);

        return $authzService->evaluate($subject, $action, $resource);
    }

    protected function hasFeatureToggled($featureName, $default = false)
    {
        if (!$this->isAuthenticated())
        {
            return $default;
        }

        $account = $this->getAuthenticationService()->getCurrentContext()->getAccount();

        /** @var FeatureToggleService $featureToggleService */
        $featureToggleService = $this->get(FeatureToggleService::SERVICE_NAME);

        return $featureToggleService->hasAccountFeatureToggled($account, $featureName, $default);
    }

    /**
     * Convenience method to add a toast message to the request/session
     *
     * @param $message string
     */
    protected function addToast($message)
    {
        $toastService = $this->container->get('CoderSpotting.ToastMessage');

        $toastService->addSuccessToast($message);
    }
}
