<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GuildCharacter;

interface CharacterSession
{
    public function addMessage(string $message) : CharacterSession;
}