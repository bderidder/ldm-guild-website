<?php

namespace LaDanse\SiteBundle\Security;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Discord\DiscordAccessToken;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LaDanseGuardAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    const AUTH_TYPE_FORM    = 0;
    const AUTH_TYPE_DISCORD = 1;

    const DISCORD_DIGEST_HEADER        = "X-LADANSE-DISCORD-AUTH-DIGEST";
    const DISCORD_IMPERSONATION_HEADER = "X-LADANSE-DISCORD-IMPERSONATION";

    private $container;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    private $em;
    private $router;
    private $passwordEncoder;
    private $csrfTokenManager;

    public function __construct(
        ContainerInterface $container,
        EntityManager $em,
        RouterInterface $router,
        UserPasswordEncoder $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->container = $container;
        $this->logger = $this->container->get("monolog.logger.ladanse");
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * This is called when an anonymous request accesses a resource that
     * requires authentication. The job of this method is to return some
     * response that "helps" the user start into the authentication process.
     *
     * Examples:
     *  A) For a form login, you might redirect to the login page
     *      return new RedirectResponse('/login');
     *  B) For an API token authentication system, you return a 401 response
     *      return new Response('Auth header required', 401);
     *
     * @param Request $request The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->isDiscordBotRequest($request))
        {
            return new Response('Auth header required', 401);
        }
        else
        {
            $url = $this->getLoginUrl();

            return new RedirectResponse($url);
        }
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array). If you return null, authentication
     * will be skipped.
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      if ($request->request->has('_username')) {
     *          return array(
     *              'username' => $request->request->get('_username'),
     *              'password' => $request->request->get('_password'),
     *          );
     *      } else {
     *          return;
     *      }
     *
     * Or for an API token that's on a header, you might use:
     *
     *      return array('api_key' => $request->headers->get('X-API-TOKEN'));
     *
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login_check' && $request->isMethod('POST');

        if ($isLoginSubmit)
        {
            $username = $request->request->get('_username');
            $password = $request->request->get('_password');
            $csrfToken = $request->request->get('_csrf_token');

            if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken)))
            {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }

            $request->getSession()->set(
                Security::LAST_USERNAME,
                $username
            );

            return [
                'type'     => LaDanseGuardAuthenticator::AUTH_TYPE_FORM,
                'username' => $username,
                'password' => $password,
            ];
        }
        else if ($this->isDiscordBotRequest($request))
        {
            return [
                'type'          => LaDanseGuardAuthenticator::AUTH_TYPE_DISCORD,
                'digest'        => $request->headers->get(LaDanseGuardAuthenticator::DISCORD_DIGEST_HEADER),
                'impersonation' => $request->headers->get(LaDanseGuardAuthenticator::DISCORD_IMPERSONATION_HEADER, null),
            ];
        }
        else
        {
            return null;
        }
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials['type'] == LaDanseGuardAuthenticator::AUTH_TYPE_FORM)
        {
            $username = $credentials['username'];

            $user = $this->em->getRepository(Account::REPOSITORY)
                ->findOneBy(['username' => $username]);

            if (!$user)
            {
                $user = $this->em->getRepository(Account::REPOSITORY)
                    ->findOneBy(['email' => $username]);
            }

            return $user;
        }
        else
        {
            if ($credentials['impersonation'])
            {
                /* @var \Doctrine\ORM\EntityRepository $accessTokenRepo */
                $accessTokenRepo = $this->em->getRepository(DiscordAccessToken::REPOSITORY);

                $discordAccessTokens = $accessTokenRepo->matching(
                    Criteria::create()->where(Criteria::expr()->eq("accessToken", $credentials['impersonation'])));

                if (count($discordAccessTokens) != 1)
                    return null;

                /** @var DiscordAccessToken $discordAccessToken */
                $discordAccessToken = $discordAccessTokens[0];

                if ($discordAccessToken->getState() != DiscordAccessToken::STATE_ACTIVE)
                    return null;

                return $discordAccessToken->getAccount();
            }
            else
            {
                return $this->em->getRepository(Account::REPOSITORY)
                    ->findOneBy(['username' => 'DiscordBot']);
            }
        }
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If any value other than true is returned, authentication will
     * fail. You may also throw an AuthenticationException if you wish
     * to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($credentials['type'] == LaDanseGuardAuthenticator::AUTH_TYPE_FORM)
        {
            $password = $credentials['password'];

            if ($this->passwordEncoder->isPasswordValid($user, $password))
            {
                return true;
            }

            return false;
        }
        else
        {
            return $this->verifyBotAuthentication($credentials['digest']);
        }
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($this->isDiscordBotRequest($request))
        {
            return new Response('Auth header required', 401);
        }
        else
        {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

            $url = $this->getLoginUrl();

            return new RedirectResponse($url);
        }
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($this->isDiscordBotRequest($request))
        {
            return null;
        }
        else
        {
            $targetPath = null;
            // if the user hit a secure page and start() was called, this was
            // the URL they were on, and probably where you want to redirect to
            $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

            if (!$targetPath)
            {
                $targetPath = $this->router->generate('menuIndex');
            }

            return new RedirectResponse($targetPath);
        }
    }

    /**
     * Does this method support remember me cookies?
     *
     * Remember me cookie will be set if *all* of the following are met:
     *  A) This method returns true
     *  B) The remember_me key under your firewall is configured
     *  C) The "remember me" functionality is activated. This is usually
     *      done by having a _remember_me checkbox in your form, but
     *      can be configured by the "always_remember_me" and "remember_me_parameter"
     *      parameters under the "remember_me" firewall key
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return true;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('fos_user_security_login');
    }

    protected function isDiscordBotRequest(Request $request)
    {
        return $request->headers->has(LaDanseGuardAuthenticator::DISCORD_DIGEST_HEADER);
    }

    protected function verifyBotAuthentication($authHeader)
    {
        $discordSecret = $this->container->getParameter('discord.bot.secret');

        $currentTimestamp = time();

        $authHeaderValues = null;

        try
        {
            /*
             * Header is of the form: <random-string>:<timestamp>:<hash>
             */
            $authHeaderValues = explode(":", $authHeader);
        }
        catch(\Exception $e)
        {
            $this->logger->debug("Given digest header value was not in the required format");

            return false;
        }

        if (count($authHeaderValues) != 3)
        {
            $this->logger->debug("Given digest header value was not in the required format");

            return false;
        }

        if (!$this->isTimestampWithinAcceptedPeriod($authHeaderValues[1], $currentTimestamp, 60))
        {
            $this->logger->debug("Timestamp was not within the accepted period");

            return false;
        }

        $ourHash = hash("sha256", $authHeaderValues[0] . ":" . $authHeaderValues[1] . ":" . $discordSecret);

        $this->logger->debug("Our hash  : " . $ourHash);
        $this->logger->debug("Given hash: " . $authHeaderValues[2]);

        return (strtolower($ourHash) == strtolower($authHeaderValues[2]));
    }

    protected function isTimestampWithinAcceptedPeriod($givenTimestamp, $ourTimestamp, $allowedDeviation)
    {
        return (($givenTimestamp > ($ourTimestamp - $allowedDeviation)) && ($givenTimestamp < ($ourTimestamp + $allowedDeviation)));
    }
}