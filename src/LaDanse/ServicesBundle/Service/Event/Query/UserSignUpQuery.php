<?php

namespace LaDanse\ServicesBundle\Service\Event\Query;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Service\Event\Command\EventDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\CommonBundle\Helper\AbstractQuery;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(UserSignUpQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class UserSignUpQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.UserSignUpQuery';

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
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var int $eventId */
    private $eventId;

    /** @var int $accountId */
    private $accountId;

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
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     * @return UserSignUpQuery
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return UserSignUpQuery
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT s ' .
            'FROM LaDanse\DomainBundle\Entity\SignUp s ' .
            'WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $this->getAccountId());
        $query->setParameter('event', $this->getEventId());

        $signUps = $query->getResult();

        if (count($signUps) === 0)
        {
            return NULL;
        }
        else
        {
            return $signUps[0];
        }
    }
}