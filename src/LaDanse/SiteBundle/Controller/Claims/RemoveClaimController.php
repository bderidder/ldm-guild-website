<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterService;
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
        /** @var CharacterService $guildCharacterService */
        $guildCharacterService = $this->get(CharacterService::SERVICE_NAME);

        $guildCharacterService->endClaim($claimId);

        return $this->redirect($this->generateUrl('viewClaims'));
    }
}
