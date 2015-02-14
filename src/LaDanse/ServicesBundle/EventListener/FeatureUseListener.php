<?php

namespace LaDanse\ServicesBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\FeatureUse;

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
     * @param $event FeatureUseEvent
     *
     * @DI\Observe(FeatureUseEvent::EVENT_NAME, priority = 255)
     */
    public function onFeatureUseEvent(FeatureUseEvent $event)
    {
        $this->logger->debug(__CLASS__ . ' received FeatureUseEvent', array('feature' => $event->getFeature()));

        $em = $this->doctrine->getManager();

        $featureUse = new FeatureUse();

        $featureUse->setFeature($event->getFeature());
        $featureUse->setUsedOn($event->getUsedOn());
        $featureUse->setUsedBy($event->getUsedBy());

        $em->persist($featureUse);
        $em->flush();
    }
}