<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\SiteBundle\Controller\Angular;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use Symfony\Component\Routing\Annotation\Route;

class CharactersController extends LaDanseController
{
    /**
     * @Route("/characters", name="charactersIndex")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(
            'LaDanseSiteBundle:angular:angular.html.twig',
            [
                'pageTitle' => 'My Characters'
            ]
        );
    }
}
