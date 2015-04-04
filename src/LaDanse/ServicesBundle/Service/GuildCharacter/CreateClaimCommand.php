<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter;

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
 * @DI\Service(CreateClaimCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class CreateClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.CreateClaimCommand';

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

    /** @var  $accountId int */
    private $accountId;
    /** @var  $characterId int */
    private $characterId;
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
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int
     */
    public function getCharacterId()
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     */
    public function setCharacterId($characterId)
    {
        $this->characterId = $characterId;
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

        /* @var $characterRepo \Doctrine\ORM\EntityRepository */
        $characterRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $character \LaDanse\DomainBundle\Entity\Character */
        $character = $characterRepo->find($this->getCharacterId());

        /* @var $accountRepo \Doctrine\ORM\EntityRepository */
        $accountRepo = $em->getRepository(Account::REPOSITORY);
        /* @var $account \LaDanse\DomainBundle\Entity\Account */
        $account = $accountRepo->find($this->getAccountId());

        $claim = new Claim();
        $claim->setCharacter($character)
            ->setAccount($account)
            ->setFromTime($onDateTime);

        if ($this->isPlaysTank())
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::TANK));
        }

        if ($this->isPlaysHealer())
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::HEALER));
        }

        if ($this->isPlaysDPS())
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::DPS));
        }

        $this->logger->info(__CLASS__ . ' persisting new claim');

        $em->persist($claim);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_CREATE,
                $account,
                array(
                    'character'   => $character->getName(),
                    'playsTank'   => $this->isPlaysTank(),
                    'playsHealer' => $this->isPlaysHealer(),
                    'playsDPS'    => $this->isPlaysDPS()
                ))
        );
    }

    private function createPlaysRole($onDateTime, $claim, $role)
    {
        $playsRole = new PlaysRole();
        $playsRole->setRole($role)
            ->setClaim($claim)
            ->setFromTime($onDateTime);

        return $playsRole;
    }
}