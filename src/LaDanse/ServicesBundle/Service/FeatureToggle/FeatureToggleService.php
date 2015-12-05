<?php

namespace LaDanse\ServicesBundle\Service\FeatureToggle;

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

    /** @var array $cachedToggles */
    private $cachedToggles = array();

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
        $toggles = $this->getFeatureTogglesForAccount($account);

        /** @var FeatureToggle $toggle */
        foreach($toggles as $toggle)
        {
            if ($toggle->getFeature() == $featureName)
            {
                return $toggle->getToggle();
            }
        }

        return $default;
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
        if (array_key_exists($account->getId(), $this->cachedToggles))
        {
            return $this->cachedToggles[$account->getId()];
        }

        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('f')
            ->from('LaDanseDomainBundle:FeatureToggle', 'f')
            ->where("f.toggleFor = ?1")
            ->setParameter(1, $account);

        $query = $qb->getQuery();

        $this->cachedToggles[$account->getId()] = $query->getResult();

        return $this->cachedToggles[$account->getId()];
    }
}