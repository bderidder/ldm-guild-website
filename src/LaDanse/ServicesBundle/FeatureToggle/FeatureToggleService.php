<?php

namespace LaDanse\ServicesBundle\FeatureToggle;

use Doctrine\ORM\QueryBuilder;
use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\FeatureToggle;
use Symfony\Component\DependencyInjection\ContainerInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SettingsService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(FeatureToggleService::SERVICE_NAME, public=true)
 */
class FeatureToggleService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.FeatureToggleService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

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
     * @param Account $account
     * @param string $featureName
     * @param bool $default
     *
     * @return string
     */
    public function hasAccountFeatureToggled(Account $account, $featureName, $default = false)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('f')
            ->from('LaDanseDomainBundle:FeatureToggle', 'f')
            ->where("f.feature = ?1")
            ->andWhere("f.toggleFor = ?2")
            ->setParameter(1, $featureName)
            ->setParameter(2, $account);

        $query = $qb->getQuery();

        $toggles = $query->getResult();

        if (count($toggles) != 0)
        {
            return $default;
        }

        /** @var FeatureToggle $featureToggle */
        $featureToggle = $toggles[0];

        return $featureToggle->getToggle();
    }

    /**
     * Return for a specific account all known feature toggles and their value
     *
     * @param Account $account
     *
     * @return array
     */
    public function getFeatureTogglesForAccount(Account $account)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('f')
            ->from('LaDanseDomainBundle:FeatureToggle', 'f')
            ->where("f.toggleFor = ?1")
            ->setParameter(1, $account);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}