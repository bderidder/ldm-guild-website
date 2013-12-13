<?php

namespace LaDanse\SiteBundle\Controller;

use \DateTime;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use LaDanse\SiteBundle\Form\Model\NewEventFormModel;
use LaDanse\SiteBundle\Form\Type\NewEventFormType;

/**
 * @Route("/switchuser")
*/
class SwitchUserController extends LaDanseController
{
	/**
     * @Route("/{id}", name="switchuser")
     * @Template("LaDanseSiteBundle::index.html.twig")
     */
    public function indexAction(Request $request, $id)
    {
		$authContext = new AuthenticationContext($this->get('LaDanse.ContainerInjector'), $request);

    	$authContext->switchUser($request, $id);
    }
}
