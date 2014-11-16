<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ListClaimsPartialController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @param string $eventId
     * @param string $accountId
     * @param string $role
     *
     * @return Response
     *
     * @Route("/{eventId}/claims/{accountId}/role/{role}", name="listClaims")
     */
    public function listAction($eventId, $accountId, $role)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->render('LaDanseSiteBundle:events:listClaims.html.twig',
                array('error' => 'Not authenticated')
            );
        }

        $event = $this->getEvent($eventId);

        if (null === $event)
        {
            $this->getLogger()->warning(__CLASS__ . ' the event does not exist in listAction',
                array("event" => $eventId));

            return $this->render('LaDanseSiteBundle:events:listClaims.html.twig',
                array('error' => 'Event does not exist')
            );
        }

        return $this->render('LaDanseSiteBundle:events:listClaims.html.twig',
            array('claims' => $this->getClaims($accountId, $role, new \DateTime()))
        );
    }

    private function getEvent($eventId)
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($eventId);

        return $event;
    }

    private function getClaims($accountId, $role, $onDateTime)
    {
        $claims = $this->getGuildCharacterService()->getClaims($accountId, $onDateTime);

        $claimsDto = array();

        foreach($claims as $claim)
        {
            $includeClaim = false;

            switch ($role)
            {
                case "Tank":
                    if ($claim->playsTank) { $includeClaim = true; };
                    break;
                case "Healer":
                    if ($claim->playsHealer) { $includeClaim = true; };
                    break;
                case "DPS":
                    if ($claim->playsDPS) { $includeClaim = true; };
                    break;
            }

            if ($includeClaim)
            {
                $character = $this->getGuildCharacterService()->getGuildCharacter($claim->character->name, $onDateTime);

                $claimsDto[] = (object)array(
                    "name"  => $character->name,
                    "level" => $character->level,
                    "race"  => $character->race->name,
                    "class" => $character->class->name
                );     
            }
        }

        return $claimsDto;
    }
}
