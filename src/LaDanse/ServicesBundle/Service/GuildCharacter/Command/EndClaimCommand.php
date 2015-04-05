<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

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
 * @DI\Service(EndClaimCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class EndClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.EndClaimCommand';

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

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        $onDateTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(Claim::REPOSITORY);
        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        $claim = $repository->find($this->getClaimId());

        $claim->setEndTime($onDateTime);

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            if (is_null($playsRole->getEndTime()))
            {
                $playsRole->setEndTime($onDateTime);
            }
        }

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_REMOVE,
                $claim->getAccount(),
                array(
                    'character'   => $claim->getCharacter()->getName()
                ))
        );
    }
}