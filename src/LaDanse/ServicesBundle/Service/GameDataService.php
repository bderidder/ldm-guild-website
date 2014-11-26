<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GameDataService
 * @package LaDanse\ServicesBundle\Service
 */
class GameDataService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GameDataService';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @return array|\LaDanse\DomainBundle\Entity\GameClass[]|\LaDanse\DomainBundle\Entity\GameRace[]
     */
    public function getAllRaces()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(GameRace::REPOSITORY)->findAll();
    }

    /**
     * @return array|\LaDanse\DomainBundle\Entity\GameClass[]
     */
    public function getAllClasses()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(GameClass::REPOSITORY)->findAll();
    }
}