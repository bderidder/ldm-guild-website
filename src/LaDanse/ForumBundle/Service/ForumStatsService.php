<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;

use LaDanse\ForumBundle\Controller\ResourceHelper;

use LaDanse\ForumBundle\Entity\ForumLastVisit;

use LaDanse\ForumBundle\Entity\Post;
use LaDanse\ForumBundle\Entity\UnreadPost;

use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @return array
     *
     * @param Account $account
     */
    public function getUnreadPostsForAccount(Account $account)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $lastVisit = $this->getLastVisitForAccount($account, new \DateTime());

        $newPosts = $this->getNewPostsSince($lastVisit);

        /** @var Post $newPost */
        foreach($newPosts as $newPost)
        {
            if ($newPost->getPoster()->getId() == $account->getId())
            {
                continue;
            }

            $unreadPost = new UnreadPost();
            $unreadPost->setId(ResourceHelper::createUUID());
            $unreadPost->setAccount($account);
            $unreadPost->setPost($newPost);

            $em->persist($unreadPost);
        }

        $em->flush();

        $this->resetLastVisitForAccount($account);

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseForumBundle::selectUnreadPostsForAccount.sql.twig')
        );
        $query->setParameter('forAccount', $account);

        $queryResult = $query->getResult();

        $unreadPosts = array();

        /** @var UnreadPost $unreadPost */
        foreach($queryResult as $unreadPost)
        {
            $unreadPosts[] = $unreadPost->getPost();
        }

        return $unreadPosts;
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
     * @param Account $account
     * @param string $postId
     */
    public function markPostAsRead(Account $account, $postId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->delete('LaDanse\ForumBundle\Entity\UnreadPost', 'u')
           ->where('u.post = :readPost')
           ->andWhere('u.account = :forAccount')
           ->setParameter('readPost', $postId)
           ->setParameter('forAccount', $account);

        $query = $qb->getQuery();

        $query->getResult();
    }

    /**
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @param \DateTime $default
     *
     * @return \DateTime
     */
    private function getLastVisitForAccount($account, \DateTime $default = null)
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
    private function resetLastVisitForAccount($account)
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
