<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Character;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(AllActiveClaimsQuery::SERVICE_NAME, public=true, shared=false)
 */
class AllActiveClaimsQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.AllActiveClaimsQuery';

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

    protected function validateInput()
    {

    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::allActiveClaims.sql.twig')
        );
        $query->setParameter('onDateTime', new \DateTime());

        $claims = $query->getResult();

        return $claims;
    }

    /**
     * @param Character $character
     * @param \DateTime $onDateTime
     *
     * @return object
     */
    protected function characterToDto(Character $character, \DateTime $onDateTime)
    {
        $activeVersion = $character->getVersionForDate($onDateTime);

        if (is_null($activeVersion))
        {
            $this->logger->info('no active version found for ' . $onDateTime->format("d/M/Y"));

            return (object)array(
                "id"       => $character->getId(),
                "fromTime" => $character->getFromTime(),
                "name"     => $character->getName()
            );
        }
        else
        {
            $this->logger->info('active version found for ' . $onDateTime->format("d/M/Y"));

            return (object)array(
                "id"       => $character->getId(),
                "fromTime" => $character->getFromTime(),
                "name"     => $character->getName(),
                "level"    => $activeVersion->getLevel(),
                "guild"    => $activeVersion->getGuild(),
                "realm"    => $character->getRealm(),
                "class"    => (object)array(
                    "id"   => $activeVersion->getGameClass()->getId(),
                    "name" => $activeVersion->getGameClass()->getName()
                ),
                "race"     => (object)array(
                    "id"   => $activeVersion->getGameRace()->getId(),
                    "name" => $activeVersion->getGameRace()->getName()
                )
            );
        }
    }

    /**
     * @var $claim Claim
     * @var $onDateTime \DateTime
     *
     * @return object
     */
    protected function claimToDto(Claim $claim, \DateTime $onDateTime)
    {
        return (object)array(
            "id"          => $claim->getId(),
            "character"   => $this->characterToDto($claim->getCharacter(), $onDateTime),
            "fromTime"    => $claim->getFromTime(),
            "playsTank"   => $claim->containsRole(Role::TANK, $onDateTime),
            "playsHealer" => $claim->containsRole(Role::HEALER, $onDateTime),
            "playsDPS"    => $claim->containsRole(Role::DPS, $onDateTime),
        );
    }
}