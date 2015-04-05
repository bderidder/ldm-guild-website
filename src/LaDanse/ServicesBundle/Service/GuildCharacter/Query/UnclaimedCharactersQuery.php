<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Query;

use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\AbstractQuery;

use LaDanse\DomainBundle\Entity\Character;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(UnclaimedCharactersQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class UnclaimedCharactersQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.UnclaimedCharactersQuery';

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

    /** @var $onDateTime \DateTime */
    private $onDateTime;

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

        $this->setOnDateTime(null);
    }

    /**
     * @return \DateTime
     */
    public function getOnDateTime()
    {
        return $this->onDateTime;
    }

    /**
     * @param \DateTime $onDateTime
     */
    public function setOnDateTime($onDateTime)
    {
        $this->onDateTime = $onDateTime;
    }

    protected function validateInput()
    {

    }

    protected function runQuery()
    {
        if ($this->getOnDateTime() == null)
        {
            // when not set, initialize to current moment
            $this->setOnDateTime(new \DateTime());
        }

        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectUnclaimedCharacters.sql.twig')
        );
        $query->setParameter('onDateTime', $this->getOnDateTime());

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters, $this->getOnDateTime());
    }

    /**
     * @param $characters
     * @param \DateTime $onDateTime
     *
     * @return array
     */
    protected function charactersToDtoArray($characters, \DateTime $onDateTime)
    {
        $charactersDto = array();

        foreach($characters as $character)
        {
            $charactersDto[] = $this->characterToDto($character, $onDateTime);
        }

        return $charactersDto;
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
}