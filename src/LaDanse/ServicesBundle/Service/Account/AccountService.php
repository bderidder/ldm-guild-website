<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Account;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AccountService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(AccountService::SERVICE_NAME, public=true)
 */
class AccountService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.AccountService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Create a new user in FOSUser
     *
     * @param $username
     * @param $password
     * @param $displayName
     * @param $email
     * @param $request
     * @param $response
     *
     * @return Account
     */
    public function createAccount($username, $password, $displayName, $email, $request, $response)
    {
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        /* @var $user \LaDanse\DomainBundle\Entity\Account */
        $user = $userManager->createUser();

        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setDisplayName($displayName);
        $user->setEmail($email);
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $user;
    }

    /**
     * @param $accountId
     *
     * @return Account
     */
    public function getAccount($accountId)
    {
        $repo = $this->getDoctrine()->getRepository(Account::REPOSITORY);

        $account = $repo->find($accountId);

        return $account;
    }

    /**
     * @param $accountId
     * @param $displayName
     * @param $email
     */
    public function updateProfile($accountId, $displayName, $email)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Account::REPOSITORY);

        $account = $repo->find($accountId);

        $account->setDisplayName($displayName);
        $account->setEmail($email);

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SETTINGS_PROFILE_UPDATE,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );
    }

    /**
     * Check if a given display name is already used by an account except the given account
     *
     * @param $login
     * @param int $exceptAccountId
     * @return bool
     */
    public function isLoginUsed($login, $exceptAccountId = 9999999)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isLoginUsed.sql.twig')
        );
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('login', $login);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    /**
     * Check if a given display name is already used by an account except the given account
     *
     * @param $displayName
     * @param int $exceptAccountId
     * @return bool
     */
    public function isDisplayNameUsed($displayName, $exceptAccountId = 9999999)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isDisplayNameUsed.sql.twig')
        );
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('displayName', $displayName);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    /**
     * Check if a given email is already used by an account except the given account
     *
     * @param $email
     * @param int $exceptAccountId
     * @return bool
     */
    public function isEmailUsed($email, $exceptAccountId = 9999999)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isEmailUsed.sql.twig')
        );
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('email', $email);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    public function updatePassword($username, $newPassword)
    {
        $userManager = $this->get('fos_user.user_manager');

        /** @var Account $user */
        $user = $userManager->findUserByUsername($username);

        if ($user == null)
        {
            return;
        }

        $user->setPlainPassword($newPassword);

        $userManager->updateUser($user);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SETTINGS_PASSWORD_UPDATE,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );
    }
}