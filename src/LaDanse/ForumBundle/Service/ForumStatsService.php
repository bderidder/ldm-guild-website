<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\ForumBundle\Entity\ForumLastVisit;

use LaDanse\ForumBundle\Controller\ResourceHelper;

/**
 * Class ForumStatisService
 *
 * @package LaDanse\ForumBundle\Service
 */
class ForumStatsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.ForumStatsService';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param \DateTime $sinceDateTime
     *
     * @return array
     */
    public function getNewPostsSince($sinceDateTime)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseForumBundle::selectNewPostsSince.sql.twig')
        );
        $query->setParameter('sinceDateTime', $sinceDateTime);

        return $query->getResult();
    }

    /**
     * @param \DateTime $sinceDateTime
     *
     * @return array
     */
    public function getNewTopicsSince($sinceDateTime)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseForumBundle::selectNewTopicsSince.sql.twig')
        );
        $query->setParameter('sinceDateTime', $sinceDateTime);

        return $query->getResult();
    }

    /**
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @param \DateTime $default
     *
     * @return \DateTime
     */
    public function getLastVisitForAccount($account, \DateTime $default = null)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('v')
            ->from('LaDanse\ForumBundle\Entity\ForumLastVisit', 'v')
            ->where(
                $qb->expr()->eq('v.account', '?1')
            )
            ->setParameter(1, $account);

        $query = $qb->getQuery();
        $result = $query->getResult();

        if (count($result) == 0)
        {
            return $default;
        }
        else
        {
            /** @var \LaDanse\ForumBundle\Entity\ForumLastVisit $forumLastVisit */
            $forumLastVisit = $result[0];

            return $forumLastVisit->getLastVisitDate();
        }
    }

    /**
     * @param \LaDanse\DomainBundle\Entity\Account $account
     */
    public function resetLastVisitForAccount($account)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('v')
            ->from('LaDanse\ForumBundle\Entity\ForumLastVisit', 'v')
            ->where(
                $qb->expr()->eq('v.account', '?1')
            )
            ->setParameter(1, $account);

        $query = $qb->getQuery();
        $result = $query->getResult();

        if (count($result) == 0)
        {
            $forumLastVisit = new ForumLastVisit();
            $forumLastVisit->setId(ResourceHelper::createUUID());
            $forumLastVisit->setAccount($account);
            $forumLastVisit->setLastVisitDate(new \DateTime());

            $em->persist($forumLastVisit);
            $em->flush();
        }
        else
        {
            /** @var \LaDanse\ForumBundle\Entity\ForumLastVisit $forumLastVisit */
            $forumLastVisit = $result[0];

            $forumLastVisit->setLastVisitDate(new \DateTime());

            $em->persist($forumLastVisit);
            $em->flush();
        }
    }
}
