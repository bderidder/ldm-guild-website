<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Forum;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Forum\ForumLastVisit;
use LaDanse\DomainBundle\Entity\Forum\Post;
use LaDanse\DomainBundle\Entity\Forum\UnreadPost;
use LaDanse\RestBundle\Controller\Forum\ResourceHelper;
use LaDanse\ServicesBundle\Common\LaDanseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ForumStatisService
 *
 * @package LaDanse\ForumBundle\Service
 *
 * @DI\Service(ForumStatsService::SERVICE_NAME, public=true)
 */
class ForumStatsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.ForumStatsService';

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
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
            $this->createSQLFromTemplate('LaDanseDomainBundle::forum\selectNewPostsSince.sql.twig')
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
            $this->createSQLFromTemplate('LaDanseDomainBundle::forum\selectUnreadPostsForAccount.sql.twig')
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
            $this->createSQLFromTemplate('LaDanseDomainBundle::forum\selectNewTopicsSince.sql.twig')
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

        $qb->delete('LaDanse\DomainBundle\Entity\Forum\UnreadPost', 'u')
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
            ->from('LaDanse\DomainBundle\Entity\Forum\ForumLastVisit', 'v')
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
            /** @var \LaDanse\DomainBundle\Entity\Forum\ForumLastVisit $forumLastVisit */
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
            ->from('LaDanse\DomainBundle\Entity\Forum\ForumLastVisit', 'v')
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
            /** @var \LaDanse\DomainBundle\Entity\Forum\ForumLastVisit $forumLastVisit */
            $forumLastVisit = $result[0];

            $forumLastVisit->setLastVisitDate(new \DateTime());

            $em->persist($forumLastVisit);
            $em->flush();
        }
    }
}
