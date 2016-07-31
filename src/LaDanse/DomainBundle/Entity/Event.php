<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachineInterface;
use LaDanse\DomainBundle\FSM\EventStateMachine;

/**
 * @ORM\Entity
 * @ORM\Table(name="Event")
 * @ORM\HasLifecycleCallbacks
 */
class Event implements StatefulInterface
{
    const REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var $inviteTime \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $inviteTime;

    /**
     * @var $startTime \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $startTime;

    /**
     * @var $endTime \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $endTime;

    /**
     * @var $lastModifiedTime \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastModifiedTime;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $topicId;

    /**
     * @ORM\OneToMany(targetEntity="SignUp", mappedBy="event", cascade={"persist", "remove"})
     */
    protected $signUps;

    /**
     * @var Account $organiser Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="organiserId", referencedColumnName="id", nullable=false)
     */
    protected $organiser;

    /**
     * @var string $state
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $state;

    private $stateMachine;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->signUps = new ArrayCollection();

        $this->initStateMachine();
    }

    private function initStateMachine()
    {
        $this->stateMachine = EventStateMachine::create();
        $this->stateMachine->setObject($this);
        $this->stateMachine->initialize();
    }

    /**
     * @ORM\PostLoad
     */
    public function doPostLoad()
    {
        $this->initStateMachine();
    }

    /**
     * @ORM\PrePersist
     */
    public function doPrePersist()
    {
        $this->lastModifiedTime = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set inviteTime
     *
     * @param \DateTime $inviteTime
     * @return Event
     */
    public function setInviteTime($inviteTime)
    {
        $this->inviteTime = $inviteTime;

        return $this;
    }

    /**
     * Get inviteTime
     *
     * @return \DateTime 
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Add signUps
     *
     * @param SignUp $signUps
     * @return Event
     */
    public function addSignUp(SignUp $signUps)
    {
        $this->signUps[] = $signUps;

        return $this;
    }

    /**
     * Remove signUps
     *
     * @param SignUp $signUps
     */
    public function removeSignUp(SignUp $signUps)
    {
        $this->signUps->removeElement($signUps);
    }

    /**
     * Get signUps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignUps()
    {
        return $this->signUps;
    }

    /**
     * Set organiser
     *
     * @param Account $organiser
     * @return Event
     */
    public function setOrganiser(Account $organiser = null)
    {
        $this->organiser = $organiser;

        return $this;
    }

    /**
     * Get organiser
     *
     * @return Account
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set lastModifiedTime
     *
     * @param \DateTime $lastModifiedTime
     * @return Event
     */
    public function setLastModifiedTime($lastModifiedTime)
    {
        $this->lastModifiedTime = $lastModifiedTime;

        return $this;
    }

    /**
     * Get lastModifiedTime
     *
     * @return \DateTime 
     */
    public function getLastModifiedTime()
    {
        return $this->lastModifiedTime;
    }

    /**
     * Set topicId
     *
     * @param string $topicId
     * @return Event
     */
    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;

        return $this;
    }

    /**
     * Get topicId
     *
     * @return string 
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * Get state
     *
     * @param string $state
     */
    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getFiniteState()
    {
        return $this->state;
    }

    /**
     * @return StateMachineInterface
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    public function toJson()
    {
        return (object) array(
            'eventId'     => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'inviteTime'  => $this->inviteTime->format(\DateTime::ISO8601),
            'startTime'   => $this->startTime->format(\DateTime::ISO8601),
            'endTime'     => $this->endTime->format(\DateTime::ISO8601),
            'organiserId' => $this->organiser->getId()
        );
    }
}
