<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Common;

use JMS\Serializer\SerializerBuilder;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\CannotEvaluateException;
use LaDanse\ServicesBundle\Service\Authorization\ResourceReference;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\FeatureToggle\FeatureToggleService;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LaDanseController
 *
 * @package LaDanse\RestBundle\Common
 */
class AbstractRestController extends Controller
{
    /**
     * Returns true if the current request is authenticated, false otherwise
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);

        $authContext = $authenticationService->getCurrentContext();

        return $authContext->isAuthenticated();
    }

    /**
     * Returns the account that is currently logged in. When not authenticated, returns null.
     *
     * @return Account
     */
    protected function getAccount()
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);

        if ($this->isAuthenticated())
        {
            return $authenticationService->getCurrentContext()->getAccount();
        }

        return null;
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

        $account = $this->getAccount();

        /** @var FeatureToggleService $featureToggleService */
        $featureToggleService = $this->get(FeatureToggleService::SERVICE_NAME);

        return $featureToggleService->hasAccountFeatureToggled($account, $featureName, $default);
    }

    /**
     * @param Request $request
     * @param string $dtoClass
     *
     * @return object
     *
     * @throws ServiceException
     */
    protected function getDtoFromContent(Request $request, string $dtoClass)
    {
        $serializer = SerializerBuilder::create()->build();

        $jsonDto = null;

        try
        {
            $jsonDto = $serializer->deserialize(
                $request->getContent(),
                $dtoClass,
                'json'
            );
        }
        catch (\Exception $exception)
        {
            throw new ServiceException($exception->getMessage(), 400);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($jsonDto);

        if (count($errors) > 0)
        {
            $errorsString = (string)$errors;

            throw new ServiceException($errorsString, 400);
        }
        else
        {
            return $jsonDto;
        }
    }
}
