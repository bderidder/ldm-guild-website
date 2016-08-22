<?php

namespace LaDanse\ServicesBundle\Service\MasterData;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\DTO\MasterData\Guild;
use LaDanse\ServicesBundle\Service\DTO\MasterData\PatchGuild;
use LaDanse\ServicesBundle\Service\DTO\MasterData\PatchRealm;
use LaDanse\ServicesBundle\Service\DTO\MasterData\Realm;

/**
 * @DI\Service(MasterDataService::SERVICE_NAME, public=true)
 */
class MasterDataService
{
    const SERVICE_NAME = 'LaDanse.MasterDataService';

    public function getAllGuilds() : array
    {

    }

    public function createGuild(PatchGuild $patchGuild) : Guild
    {

    }

    public function updateGuild(string $guildId, PatchGuild $patchGuild)
    {

    }

    public function removeGuild(string $guildId)
    {

    }

    public function getAllRealms() : array
    {

    }

    public function createRealm(PatchRealm $patchRealm) : Realm
    {

    }

    public function updateRealm(string $realmId, PatchRealm $patchRealm)
    {

    }

    public function reamoveRealm(string $realmId)
    {

    }
}