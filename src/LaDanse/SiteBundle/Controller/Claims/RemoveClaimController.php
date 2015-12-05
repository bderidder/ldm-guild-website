<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

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
    	$this->getGuildCharacterService()->endClaim($claimId);

        return $this->redirect($this->generateUrl('viewClaims'));
    }
}
