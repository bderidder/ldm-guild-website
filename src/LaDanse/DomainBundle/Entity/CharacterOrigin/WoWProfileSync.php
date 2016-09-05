<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\CharacterOrigin;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="WoWProfileSync", options={"collate":"utf8mb4_unicode_ci", "charset":"utf8mb4"})
 */
class WoWProfileSync extends CharacterSource
{
    const REPOSITORY = 'LaDanseDomainBundle:CharacterOrigin\WoWProfileSync';

    /**
     * @ORM\ManyToOne(targetEntity=Account::class)
     * @ORM\JoinColumn(name="account", referencedColumnName="id", nullable=false)
     *
     * @var Account $account
     */
    protected $account;

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     * @return WoWProfileSync
     */
    public function setAccount(Account $account): WoWProfileSync
    {
        $this->account = $account;
        return $this;
    }
}