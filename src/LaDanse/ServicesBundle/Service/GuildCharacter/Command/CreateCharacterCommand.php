<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use LaDanse\DomainBundle\Entity\CharacterVersion;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\CommonBundle\Helper\AbstractCommand;

use LaDanse\DomainBundle\Entity\Character;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(CreateCharacterCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class CreateCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.CreateCharacterCommand';

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

    /** @var $name string  */
    private $name;
    /** @var $level int */
    private $level;
    /** @var $gameRace GameRace */
    private $gameRace;
    /** @var $gameClass GameClass */
    private $gameClass;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return GameRace
     */
    public function getGameRace()
    {
        return $this->gameRace;
    }

    /**
     * @param GameRace $gameRace
     */
    public function setGameRace(GameRace $gameRace)
    {
        $this->gameRace = $gameRace;
    }

    /**
     * @return GameClass
     */
    public function getGameClass()
    {
        return $this->gameClass;
    }

    /**
     * @param GameClass $gameClass
     */
    public function setGameClass(GameClass $gameClass)
    {
        $this->gameClass = $gameClass;
    }

    protected function validateInput()
    {

    }

    protected function runCommand()
    {
        $importInstant = new \DateTime();

        $em = $this->doctrine->getManager();

        $character = new Character();
        $character->setName($this->getName());
        $character->setFromTime($importInstant);

        $version = new CharacterVersion();
        $version->setCharacter($character);
        $version->setLevel($this->getLevel());
        $version->setFromTime($importInstant);
        $version->setGameClass($this->getGameClass());
        $version->setGameRace($this->getGameRace());

        $em->persist($character);
        $em->persist($version);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_CREATE,
                null,
                array(
                    'character'   => $this->getName(),
                )
            )
        );
    }
}