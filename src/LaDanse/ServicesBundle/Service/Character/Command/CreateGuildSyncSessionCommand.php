<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\CharacterOrigin\GuildSync;
use LaDanse\DomainBundle\Entity\GameData\Guild;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(CreateGuildSyncSessionCommand::SERVICE_NAME, public=true, shared=false)
 */
class CreateGuildSyncSessionCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.CreateGuildSyncSessionCommand';

    /**
     * @var \Monolog\Logger $logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var StringReference $guildId */
    private $guildId;

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
     * @return StringReference
     */
    public function getGuildId(): StringReference
    {
        return $this->guildId;
    }

    /**
     * @param StringReference $guildId
     * @return CreateGuildSyncSessionCommand
     */
    public function setGuildId(StringReference $guildId): CreateGuildSyncSessionCommand
    {
        $this->guildId = $guildId;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->guildId == null)
        {
            throw new InvalidInputException("Given guild id was null");
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('gs')
            ->from(GuildSync::class, 'gs')
            ->where('gs.guild = ?1')
            ->setParameter(1, $em->getReference(Guild::class, $this->getGuildId()));

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $sources = $query->getResult();

        /** @var GuildSync $guildSync */
        $guildSync = null;

        if (count($sources) == 0)
        {
            // there is no GuildSync yet for this guild, create it on the fly

            $guildSync = new GuildSync();
            $guildSync->setGuild($em->getReference(Guild::class, $this->getGuildId()));

            $em->persist($guildSync);
        }
        else if (count($sources) == 1)
        {
            $guildSync = $sources[0];
        }
        else
        {
            throw new ServiceException(
                sprintf(
                    "Found multiple GuildSync instances for the guild %s",
                    $this->getGuildId()
                ),
                500
            );
        }

        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $this->container->get(CharacterSessionImpl::SERVICE_NAME);

        $characterSessionImpl->startSession($guildSync);

        return $characterSessionImpl;
    }
}