<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\PlaysRole;
use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\CommonBundle\Helper\AbstractCommand;

use LaDanse\DomainBundle\Entity\Character;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(UpdateClaimCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class UpdateClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.UpdateClaimCommand';

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

    /** @var  $claimId int */
    private $claimId;
    /** @var  $playsTank bool */
    private $playsTank;
    /** @var  $playsHealer bool */
    private $playsHealer;
    /** @var  $playsDps bool */
    private $playsDPS;

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
    public function getClaimId()
    {
        return $this->claimId;
    }

    /**
     * @param int $claimId
     */
    public function setClaimId($claimId)
    {
        $this->claimId = $claimId;
    }

    /**
     * @return boolean
     */
    public function isPlaysTank()
    {
        return $this->playsTank;
    }

    /**
     * @param boolean $playsTank
     */
    public function setPlaysTank($playsTank)
    {
        $this->playsTank = $playsTank;
    }

    /**
     * @return boolean
     */
    public function isPlaysHealer()
    {
        return $this->playsHealer;
    }

    /**
     * @param boolean $playsHealer
     */
    public function setPlaysHealer($playsHealer)
    {
        $this->playsHealer = $playsHealer;
    }

    /**
     * @return boolean
     */
    public function isPlaysDPS()
    {
        return $this->playsDPS;
    }

    /**
     * @param boolean $playsDPS
     */
    public function setPlaysDPS($playsDPS)
    {
        $this->playsDPS = $playsDPS;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        $onDateTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /* @var $claimRepo \Doctrine\ORM\EntityRepository */
        $claimRepo = $em->getRepository(Claim::REPOSITORY);
        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        $claim = $claimRepo->find($this->getClaimId());

        $this->checkAndUpdateRole($em, $claim, Role::TANK, $this->isPlaysTank(), $onDateTime);
        $this->checkAndUpdateRole($em, $claim, Role::HEALER, $this->isPlaysHealer(), $onDateTime);
        $this->checkAndUpdateRole($em, $claim, Role::DPS, $this->isPlaysDPS(), $onDateTime);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_EDIT,
                $claim->getAccount(),
                array(
                    'character'   => $claim->getCharacter()->getName(),
                    'playsTank'   => $this->isPlaysTank(),
                    'playsHealer' => $this->isPlaysHealer(),
                    'playsDPS'    => $this->isPlaysHealer()
                ))
        );
    }

    private function checkAndUpdateRole(ObjectManager $em, Claim $claim, $roleName, $willPlayRole, $onDateTime)
    {
        $alreadyPlaysRole = false;

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            // remember if the role is currently active (present and endTime not set)
            if ($playsRole->isRole($roleName) and is_null($playsRole->getEndTime()))
            {
                $alreadyPlaysRole = true;
            }
        }

        if (!$alreadyPlaysRole and $willPlayRole)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, $roleName));

            $this->logger->info(__CLASS__ . ' added ' . $roleName . ' role to claim ' . $this->getClaimId());
        }

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            // if the role is currently active (present and endTime not set)
            // and the player will not play it anymore, set endTime
            if ($playsRole->isRole($roleName) and !$willPlayRole and is_null($playsRole->getEndTime()))
            {
                $playsRole->setEndTime($onDateTime);

                $this->logger->info(__CLASS__ . ' removed ' . $roleName . ' role from claim ' . $this->getClaimId());
            }
        }

        $em->flush();
    }

    private function createPlaysRole($onDateTime, Claim $claim, $role)
    {
        $playsRole = new PlaysRole();
        $playsRole->setRole($role)
            ->setClaim($claim)
            ->setFromTime($onDateTime);

        $claim->addRole($playsRole);

        return $playsRole;
    }
}