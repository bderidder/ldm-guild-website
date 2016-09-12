<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\CharacterOrigin\CharacterSource;
use LaDanse\DomainBundle\Entity\CharacterOrigin\CharacterSyncSession;
use LaDanse\ServicesBundle\Service\Character\CharacterSession;
use LaDanse\ServicesBundle\Service\Character\InvalidSessionStatException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(CharacterSessionImpl::SERVICE_NAME, public=true, shared=false)
 */
class CharacterSessionImpl implements CharacterSession
{
    const SERVICE_NAME = 'LaDanse.CharacterSessionImpl';

    use ContainerAwareTrait;

    /**
     * @var \Monolog\Logger $logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var CharacterSource $characterSource */
    private $characterSource;

    /** @var CharacterSyncSession $syncSession */
    private $syncSession;

    /** @var string $sessionState */
    private $sessionState;

    /** @var array $logMessages */
    private $logMessages;

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

        $this->logMessages = [];
        $this->sessionState = 'CONSTRUCTED';
    }

    /**
     * @return CharacterSource
     */
    public function getCharacterSource(): CharacterSource
    {
        return $this->characterSource;
    }

    public function startSession(CharacterSource $characterSource) : CharacterSessionImpl
    {
        if ($this->sessionState != 'CONSTRUCTED')
        {
            throw new InvalidSessionStatException(
                "Session is not in state CONSTRUCTED but in state " . $this->sessionState
            );
        }

        $this->characterSource = $characterSource;

        $em = $this->doctrine->getManager();

        $this->syncSession = new CharacterSyncSession();
        $this->syncSession->setFromTime(new \DateTime());
        $this->syncSession->setCharacterSource($characterSource);

        $em->persist($this->syncSession);
        $em->flush();

        $this->sessionState = 'STARTED';

        return $this;
    }

    public function endSession() : CharacterSessionImpl
    {
        if ($this->sessionState != 'STARTED')
        {
            throw new InvalidSessionStatException(
                "Session is not in state STARTED but in state " . $this->sessionState
            );
        }

        $em = $this->doctrine->getManager();

        $this->syncSession->setEndTime(new \DateTime());
        $this->syncSession->setLog(json_encode($this->logMessages));

        $em->flush();

        $this->sessionState = 'ENDED';

        return $this;
    }

    public function addMessage(string $message) : CharacterSession
    {
        if ($this->sessionState != 'STARTED')
        {
            throw new InvalidSessionStatException(
                "Session is not in state STARTED but in state " . $this->sessionState
            );
        }

        $this->logMessages[] = $message;

        return $this;
    }
}