<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterSession;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PatchCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class PatchCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PatchCharacterCommand';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var CharacterSession */
    private $characterSession;

    /** @var int */
    private $characterId;

    /** @var DTO\Character\PatchCharacter */
    private $patchCharacter;

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
     * @return CharacterSession
     */
    public function getCharacterSession(): CharacterSession
    {
        return $this->characterSession;
    }

    /**
     * @param CharacterSession $characterSession
     * @return PatchCharacterCommand
     */
    public function setCharacterSession(CharacterSession $characterSession): PatchCharacterCommand
    {
        $this->characterSession = $characterSession;
        return $this;
    }

    /**
     * @return int
     */
    public function getCharacterId(): int
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     * @return PatchCharacterCommand
     */
    public function setCharacterId(int $characterId): PatchCharacterCommand
    {
        $this->characterId = $characterId;
        return $this;
    }

    /**
     * @return DTO\Character\PatchCharacter
     */
    public function getPatchCharacter(): DTO\Character\PatchCharacter
    {
        return $this->patchCharacter;
    }

    /**
     * @param DTO\Character\PatchCharacter $patchCharacter
     * @return PatchCharacterCommand
     */
    public function setPatchCharacter(DTO\Character\PatchCharacter $patchCharacter): PatchCharacterCommand
    {
        $this->patchCharacter = $patchCharacter;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->getPatchCharacter() == null || $this->getCharacterSession() == null)
        {
            throw new InvalidInputException("characterSession or patchCharacter can't be null");
        }

        if (!($this->getCharacterSession() instanceof CharacterSessionImpl))
        {
            throw new InvalidInputException("Unrecognized CharacterSession implementation");
        }
    }

    protected function runCommand()
    {
        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $this->getCharacterSession();

        /**
         * TODO
         *
         * check if character already exists (name + realm as combined unique key)
         *  if it already exists
         *      verify if the characterSource isn't already tracking this character
         *          if it is already being tracked, throw exception as it should do a PUT and not a POST
         *          if it is not being tracked, add tracker
         *      define a delta and update character
         *  if it does not exist
         *      create character and add tracker
         */

        // create a shared $fromTime since we will need it often below
        $fromTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('c')
            ->from(Entity\Character::class, 'c')
            ->join('c.realm', 'realm')
            ->where('c.name = ?1')
            ->andWhere('realm.id = ?2')
            ->setParameter(1, $this->getPatchCharacter()->getName())
            ->setParameter(
                2,
                $em->getReference(Entity\GameData\Realm::class, $this->getPatchCharacter()->getRealmReference()->getId()                )
            );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            // character does not yet exist, should never happen
        }
        elseif(count($characters) != 1)
        {
            // this should never happen, we cannot have two characters with the same name on the same realm

            throw new \Exception(
                "Two characters with the same name on the same realm found",
                [
                    "name"  => $this->getPatchCharacter()->getName(),
                    "realm" => $this->getPatchCharacter()->getRealmReference()->getId()
                ]
            );
        }

        return null;
    }
}