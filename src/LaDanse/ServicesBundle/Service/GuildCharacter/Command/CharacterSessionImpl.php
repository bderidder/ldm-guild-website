<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterSession;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(CharacterSessionImpl::SERVICE_NAME, public=true)
 */
class CharacterSessionImpl implements CharacterSession
{
    const SERVICE_NAME = 'LaDanse.CharacterSessionImpl';

    use ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function startSession() : CharacterSessionImpl
    {
        return $this;
    }

    public function endSession() : CharacterSessionImpl
    {
        return $this;
    }

    public function addMessage(string $message)
    {
        // TODO: Implement addMessage() method.
    }
}