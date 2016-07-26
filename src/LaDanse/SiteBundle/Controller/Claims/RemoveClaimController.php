<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;

class RemoveClaimController extends LaDanseController
{
    /**
     * @param string $claimId
     *
     * @return Response
     *
     * @Route("/{claimId}/remove", name="removeClaim")
     */
    public function removeAction($claimId)
    {
        /** @var GuildCharacterService $guildCharacterService */
        $guildCharacterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->endClaim($claimId);

        return $this->redirect($this->generateUrl('viewClaims'));
    }
}
