<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SignUp")
 */
class SignUp
{
    const REPOSITORY = 'LaDanseDomainBundle:SignUp';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="signUps")
     * @ORM\JoinColumn(name="eventId", referencedColumnName="id", nullable=false)
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="ForRole", mappedBy="signUp", cascade={"persist", "remove"})
     * @var $roles ArrayCollection
     */
    protected $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * Set event
     *
     * @param Event $event
     * @return SignUp
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return SignUp
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Add roles
     *
     * @param ForRole $roles
     * @return SignUp
     */
    public function addRole(ForRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param ForRole $roles
     */
    public function removeRole(ForRole $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SignUp
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    public function toJson()
    {
        $simpleRoles = array();

        for($i = 0; $i < $this->roles->count(); $i++)
        {
            $simpleRoles[] = $this->roles->get($i)->getRole();
        }

        return (object) array(
            'signUpId' => $this->id,
            'type'     => $this->type,
            'roles'    => $simpleRoles
        );
    }
}
