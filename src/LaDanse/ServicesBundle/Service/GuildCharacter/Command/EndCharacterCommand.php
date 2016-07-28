<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Character;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(EndCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class EndCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.EndCharacterCommand';

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

    private $characterId;

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

    protected function validateInput()
    {

    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();
        $repo = $this->doctrine->getRepository(Character::REPOSITORY);

        $character = $repo->find($this->getCharacterId());

        $character->setEndTime(new \DateTime());

        $em->flush();

        $this->endClaimsForCharacter($character);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_REMOVE,
                null,
                array(
                    'character' => $character->getName()
                )
            )
        );
    }

    private function endClaimsForCharacter($character)
    {
        $onDateTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaimsForCharacter.sql.twig')
        );

        $query->setParameter('character', $character);

        $claims = $query->getResult();

        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        foreach($claims as $claim)
        {
            $claim->setEndTime($onDateTime);

            /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
            foreach($claim->getRoles() as $playsRole)
            {
                $playsRole->setEndTime($onDateTime);
            }
        }

        $em->flush();
    }
}