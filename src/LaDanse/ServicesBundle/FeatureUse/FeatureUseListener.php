<?php

namespace LaDanse\ServicesBundle\FeatureUse;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\DomainBundle\Entity\FeatureUse;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

/**
 * @DI\Service(FeatureUseListener::SERVICE_NAME, public=true)
 */
class FeatureUseListener
{
    const SERVICE_NAME = 'LaDanse.FeatureUseListener';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @param $activity ActivityEvent
     *
     * @DI\Observe(ActivityEvent::EVENT_NAME, priority = 255)
     */
    public function onActivityEvent(ActivityEvent $activity)
    {
        $this->logger->debug(
            __CLASS__ . ' received ActivityEvent',
            array(
                'activity' => $activity->getType(),
                'data' => $activity->getObject()
            )
        );

        $em = $this->doctrine->getManager();

        $featureUse = new FeatureUse();

        $featureUse->setFeature($activity->getType());
        $featureUse->setUsedOn($activity->getTime());
        $featureUse->setData($activity->getObject());
        $featureUse->setUsedBy($activity->getActor());

        $em->persist($featureUse);
        $em->flush();
    }
}