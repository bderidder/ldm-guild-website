<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\DomainBundle\Entity\Product;

/**
 * @Route("/Events/Create")
*/
class CreateEventController extends Controller
{
	/**
     * @Route("/", name="createEventIndex")
     * @Template("LaDanseSiteBundle::createEvent.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$product = new Product();
    	$product->setName('A Foo Bar');
    	$product->setPrice('19.99');
    	$product->setDescription('Lorem ipsum dolor');

    	$em = $this->getDoctrine()->getManager();
    	$em->persist($product);
    	$em->flush();
    }
}
