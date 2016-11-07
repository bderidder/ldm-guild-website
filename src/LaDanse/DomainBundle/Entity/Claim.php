<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CharacterClaim")
 */
class Claim extends VersionedEntity
{
    const REPOSITORY = 'LaDanseDomainBundle:Claim';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

    /**
     * @ORM\ManyToOne(targetEntity="Character")
     * @ORM\JoinColumn(name="characterId", referencedColumnName="id", nullable=false)
     */
    protected $character;

    /**
     * @ORM\OneToMany(targetEntity="PlaysRole", mappedBy="claim", cascade={"persist", "remove"})
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
     * Set account
     *
     * @param Account $account
     * @return Claim
     */
    public function setAccount(Account $account)
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
     * Set character
     *
     * @param Character $character
     * @return Claim
     */
    public function setCharacter(Character $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return Character
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Add roles
     *
     * @param PlaysRole $roles
     * @return Claim
     */
    public function addRole(PlaysRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param PlaysRole $roles
     */
    public function removeRole(PlaysRole $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function containsRole($roleName, \DateTime $onDateTime)
    {
        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($this->getRoles() as $playsRole)
        {
            if (($playsRole->isRole($roleName))
                &&
                (($playsRole->getFromTime()->getTimestamp() <= $onDateTime->getTimestamp())
                    && (is_null($playsRole->getEndTime()) ||
                        ($playsRole->getEndTime()->getTimestamp() > $onDateTime->getTimestamp())))
            )
            {
                return true;
            }
        }

        return false;
    }
}
