<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\CharacterOrigin;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\GameData\Guild;

/**
 * @ORM\Entity
 * @ORM\Table(name="GuildSync", options={"collate":"utf8mb4_unicode_ci", "charset":"utf8mb4"})
 */
class GuildSync extends CharacterSource
{
    const REPOSITORY = 'LaDanseDomainBundle:CharacterOrigin\GuildSync';

    /**
     * @ORM\ManyToOne(targetEntity=Guild::class)
     * @ORM\JoinColumn(name="guild", referencedColumnName="id", nullable=false)
     *
     * @var Guild $guild
     */
    protected $guild;

    /**
     * @return Guild
     */
    public function getGuild(): Guild
    {
        return $this->guild;
    }

    /**
     * @param Guild $guild
     * @return GuildSync
     */
    public function setGuild(Guild $guild): GuildSync
    {
        $this->guild = $guild;
        return $this;
    }
}