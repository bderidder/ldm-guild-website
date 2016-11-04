<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SocialConnect;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AccountService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(SocialConnectProvider::SERVICE_NAME, public=true)
 */
class SocialConnectProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    const SERVICE_NAME = 'LaDanse.SocialConnectProvider';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var $logger \LaDanse\SiteBundle\Security\AuthenticationService
     * @DI\Inject("LaDanse.AuthenticationService")
     */
    public $authService;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $properties = [
        'identifier' => 'id',
    ];

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     *
     * @DI\InjectParams({
     *     "userManager" = @DI\Inject("fos_user.user_manager")
     * })
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
        //$this->properties  = array_merge($this->properties, $properties);
        $this->accessor    = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $this->logger->info("loadUserByOAuthUserResponse(username) " . $response->getUsername());
        $this->logger->info("loadUserByOAuthUserResponse(resource) " . $response->getResourceOwner()->getName());

        $username = $response->getUsername();

        $authContext = $this->authService->getCurrentContext();

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('sc')
            ->from('LaDanse\DomainBundle\Entity\SocialConnect', 'sc')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('sc.resource', '?1'),
                $qb->expr()->eq('sc.resourceId', '?2')
            ))
            ->setParameter(1, $response->getResourceOwner()->getName())
            ->setParameter(2, $response->getUsername());

        $result = $qb->getQuery()->getResult();

        if (count($result) == 0)
        {
            $this->logger->info(__CLASS__ . sprintf(' we found no SocialConnect for %s at %s', $username, $response->getResourceOwner()->getName()));

            if ($authContext->isAuthenticated() && !(null === $username))
            {
                $this->logger->info(__CLASS__ . sprintf(' the user is already authenticated as %s, creating SocialConnect ', $authContext->getAccount()->getUsername()));

                $socialConnect = new SocialConnect();
                $socialConnect->setAccount($authContext->getAccount());
                $socialConnect->setResource($response->getResourceOwner()->getName());
                $socialConnect->setResourceId($response->getUsername());
                $socialConnect->setAccessToken($response->getAccessToken());
                $socialConnect->setRefreshToken($response->getRefreshToken());
                $socialConnect->setConnectTime(new \DateTime());

                $this->doctrine->getManager()->persist($socialConnect);
                $this->doctrine->getManager()->flush();

                return $authContext->getAccount();
            }
        }
        else
        {
            /** @var SocialConnect $socialConnect */
            $socialConnect = $result[0];

            $this->logger->info(__CLASS__ . sprintf(' we found an existing SocialConnect %s', $socialConnect->getAccount()->getUsername()));

            return $socialConnect->getAccount();
        }

        $this->logger->info('oAuthToken ' . $response->getOAuthToken());

        foreach($response->getResponse() as $path)
        {
            $this->logger->info('path element > ' . json_encode($path));
        }

        throw new AccountNotLinkedException(sprintf("Resource '%s' not found and no user currently authenticated", $response->getResourceOwner()->getName()));
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $this->logger->info("connect " . $response->getResourceOwner()->getName());

        if (!$user instanceof Account)
        {
            throw new UnsupportedUserException(sprintf('Expected an instance of LaDanse\DomainBundle\Entity\Account, but got "%s".', get_class($user)));
        }

        $socialConnect = new SocialConnect();
        $socialConnect->setAccount($user);
        $socialConnect->setResource($response->getResourceOwner()->getName());
        $socialConnect->setResourceId($response->getUsername());
        $socialConnect->setAccessToken($response->getAccessToken());
        $socialConnect->setRefreshToken($response->getRefreshToken());
        $socialConnect->setConnectTime(new \DateTime());

        $this->doctrine->getManager()->persist($socialConnect);
        $this->doctrine->getManager()->flush();

        $this->userManager->updateUser($user);
    }
}
