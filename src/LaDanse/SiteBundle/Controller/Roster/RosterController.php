<?php

namespace LaDanse\SiteBundle\Controller\Roster;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Account\AccountService;

use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Model\Roster\AccountAndClaims;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class RosterController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @return Response
     *
     * @Route("/", name="viewRoster")
     */
    public function viewAction()
    {
        /** @var GuildCharacterService $guildCharacterService */
        $guildCharacterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $claims = $guildCharacterService->getAllActiveClaims();

        return $this->render(
            'LaDanseSiteBundle:roster:viewRoster.html.twig',
            array(
                'claims' => $this->createAccountAndClaimsModel($claims)
            )
        );
    }

    /**
     * @param array $claims
     *
     * @return array
     */
    private function createAccountAndClaimsModel(array $claims)
    {
        $result = array();

        /** @var Claim $claim */
        foreach($claims as $claim)
        {
            $accountId = $claim->getAccount()->getId();
            $displayName = $claim->getAccount()->getDisplayName();
            $charName = $claim->getCharacter()->getName();

            $accountAndClaims = $this->retrieveAccount($result, $accountId);

            if ($accountAndClaims == null)
            {
                $accountAndClaims = new AccountAndClaims();
                $accountAndClaims->setId($accountId);
                $accountAndClaims->setDisplayName($displayName);

                $result[] = $accountAndClaims;
            }

            $accountAndClaims->addClaim($charName);
        }

        usort(
            $result,
            function (AccountAndClaims $a, AccountAndClaims $b)
            {
                return strcmp($a->getDisplayName(), $b->getDisplayName());
            }
        );

        return $result;
    }

    private function retrieveAccount($accountAndClaims, $accountId)
    {
        /** @var AccountAndClaims $elem */
        foreach($accountAndClaims as $elem)
        {
            if ($elem->getId() == $accountId)
            {
                return $elem;
            }
        }

        return null;
    }
}