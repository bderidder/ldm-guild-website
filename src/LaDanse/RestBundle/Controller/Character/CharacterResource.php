<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Character;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class CharacterResource extends AbstractRestController
{
    /**
     * @param Request $request
     * @param $guildId
     *
     * @return Response
     *
     * @Route("/guilds/{guildId}", name="getAllCharacters")
     * @Method({"GET"})
     */
    public function getAllCharacters(Request $request, $guildId)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        $characters = $characterService->getAllCharactersInGuild(
            new StringReference($guildId)
        );

        return new JsonResponse(ResourceHelper::array($characters));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/test", name="testAction")
     * @Method({"GET"})
     */
    public function testAction(Request $request)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        $characterSession = $characterService->createGuildSyncSession(
            new StringReference("ec3f7892-744e-11e6-ad39-cd9eae90c52b")
        );

        $patchCharacter = new PatchCharacter();

        $patchCharacter
            ->setName('Test')
            ->setLevel(90)
            ->setGuildReference(new StringReference("ec3f7892-744e-11e6-ad39-cd9eae90c52b"))
            ->setGameClassReference(new StringReference("eaaf2072-744e-11e6-ad39-cd9eae90c52b"))
            ->setGameRaceReference(new StringReference("eb758c62-744e-11e6-ad39-cd9eae90c52b"))
            ->setRealmReference(new StringReference("ec3fa222-744e-11e6-ad39-cd9eae90c52b"));

        try
        {
            $characterDto = $characterService->patchCharacter($characterSession, 1236, $patchCharacter);

            return new JsonResponse(ResourceHelper::object($characterDto));
        }
        catch(ServiceException $exception)
        {
            return ResourceHelper::createErrorResponse($request, $exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/test2", name="secondTestAction")
     * @Method({"GET"})
     */
    public function secondTestAction(Request $request)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        $characterSession = $characterService->createGuildSyncSession(
            new StringReference("ec3f7892-744e-11e6-ad39-cd9eae90c52b")
        );

        try
        {
            $characterService->untrackCharacter($characterSession, 1236);

            return new JsonResponse(ResourceHelper::object(null));
        }
        catch(ServiceException $exception)
        {
            return ResourceHelper::createErrorResponse($request, $exception->getCode(), $exception->getMessage());
        }
    }
}
