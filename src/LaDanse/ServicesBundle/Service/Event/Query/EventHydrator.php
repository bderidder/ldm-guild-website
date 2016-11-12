<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Event\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;

/**
 * @DI\Service(EventHydrator::SERVICE_NAME, public=true, shared=false)
 */
class EventHydrator
{
    const SERVICE_NAME = 'LaDanse.EventHydrator';

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

    /** @var array $eventIds */
    private $eventIds;

    /** @var $onDateTime \DateTime */
    private $onDateTime;

    /** @var bool $initialized */
    private $initialized = false;

    /** @var array $signUps */
    private $signUps;

    /** @var array $forRoles */
    private $forRoles;

    /**
     * @return array
     */
    public function getEventIds(): array
    {
        return $this->eventIds;
    }

    /**
     * @param array $eventIds
     * @return EventHydrator
     */
    public function setEventIds(array $eventIds): EventHydrator
    {
        $this->eventIds = $eventIds;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOnDateTime(): \DateTime
    {
        return $this->onDateTime;
    }

    /**
     * @param \DateTime $onDateTime
     * @return EventHydrator
     */
    public function setOnDateTime(\DateTime $onDateTime): EventHydrator
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    /**
     * @param int $eventId
     *
     * @return array
     */
    public function getSignUps(int $eventId)
    {
        $this->init();

        if ($this->signUps == null)
        {
            return [];
        }

        $result = [];

        foreach($this->signUps as $signUp)
        {
            /** @var Entity\SignUp $signUp */
            if ($signUp->getEvent()->getId() == $eventId)
            {
                $result[] = $signUp;
            }
        }

        return $result;
    }

    /**
     * @param int $signUpId
     *
     * @return array
     */
    public function getForRoles(int $signUpId)
    {
        $this->init();

        if ($this->forRoles == null)
        {
            return [];
        }

        $result = [];

        foreach($this->forRoles as $forRole)
        {
            /** @var Entity\ForRole $forRole */
            if ($forRole->getSignUp()->getId() == $signUpId)
            {
                $result[] = $forRole;
            }
        }

        return $result;
    }

    private function init()
    {
        if ($this->initialized)
            return;

        if ($this->getEventIds() == null || count($this->getEventIds()) == 0)
        {
            $this->signUps = [];
            $this->initialized = true;

            return;
        }

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('signUp', 'account', 'event')
            ->from(Entity\SignUp::class, 'signUp')
            ->join('signUp.event', 'event')
            ->join('signUp.account', 'account')
            ->add('where',
                $qb->expr()->in(
                    'event.id',
                    $this->getEventIds()
                )
            );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $this->signUps = $query->getResult();

        $signUpIds = [];

        foreach($this->signUps as $signUp)
        {
            /** @var Entity\SignUp $signUp */

            $signUpIds[] = $signUp->getId();
        }

        if (count($signUpIds) == 0)
        {
            $this->forRoles = [];
        }
        else
        {
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->select('forRole', 'signUp')
                ->from(Entity\ForRole::class, 'forRole')
                ->join('forRole.signUp', 'signUp')
                ->add('where',
                    $qb->expr()->in(
                        'signUp.id',
                        $signUpIds
                    )
                );

            /* @var $query \Doctrine\ORM\Query */
            $query = $qb->getQuery();

            $this->forRoles = $query->getResult();
        }

        $this->initialized = true;
    }
}