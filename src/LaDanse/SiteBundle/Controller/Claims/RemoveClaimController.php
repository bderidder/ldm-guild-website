<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use \DateTime;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Form\Model\NewClaimFormModel;
use LaDanse\SiteBundle\Form\Type\NewClaimFormType;

use LaDanse\SiteBundle\Model\ErrorModel;

class RemoveClaimController extends LaDanseController
{
    /**
     * @Route("/{claimId}/remove", name="removeClaim")
     */
    public function removeAction($claimId)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in viewClaims');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $this->getClaimsService()->endClaim($claimId);

        return $this->redirect($this->generateUrl('viewClaims'));
    }
}
