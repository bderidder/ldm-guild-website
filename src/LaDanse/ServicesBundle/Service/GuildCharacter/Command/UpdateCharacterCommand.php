<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Character;
use LaDanse\DomainBundle\Entity\CharacterVersion;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(UpdateCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class UpdateCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.UpdateCharacterCommand';

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

    /** @var $characterId int  */
    private $characterId;
    /** @var $name string  */
    private $name;
    /** @var $level int */
    private $level;
    /** @var $gameRace GameRace */
    private $gameRace;
    /** @var $gameClass GameClass */
    private $gameClass;
    /** @var string $guild */
    private $guild;

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

    /**
     * @return string
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * @param string $guild
     */
    public function setGuild($guild)
    {
        $this->guild = $guild;
    }

    protected function validateInput()
    {

    }

    protected function runCommand()
    {
        $updateInstant = new \DateTime();

        $em = $this->doctrine->getManager();

        /* @var $charRepo \Doctrine\ORM\EntityRepository */
        $charRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $character \LaDanse\DomainBundle\Entity\Character */
        $character = $charRepo->find($this->getCharacterId());

        $oldCharacter = clone($character);

        /**
         * @var $oldCharacterVersion CharacterVersion
         */
        $oldCharacterVersion = null;

        foreach($character->getVersions() as $charVersion)
        {
            if (is_null($charVersion->getEndTime()))
            {
                $charVersion->setEndTime($updateInstant);

                $oldCharacterVersion = clone($charVersion);
            }
        }

        $version = new CharacterVersion();
        $version->setCharacter($character);
        $version->setLevel($this->getLevel());
        $version->setFromTime($updateInstant);
        $version->setGameClass($this->getGameClass());
        $version->setGameRace($this->getGameRace());
        $version->setGuild($this->getGuild());

        $em->persist($character);
        $em->persist($version);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_UPDATE,
                null,
                array(
                    'oldName'   => $oldCharacter->getName(),
                    'oldLevel'  => $oldCharacterVersion->getLevel(),
                    'newName'   => $this->getName(),
                    'newLevel'  => $this->getLevel()
                )
            )
        );
    }
}