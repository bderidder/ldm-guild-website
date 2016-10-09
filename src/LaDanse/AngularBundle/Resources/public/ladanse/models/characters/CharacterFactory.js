/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.CharacterFactory = function()
{
    this.createSingleCharacter = function(gameDataModel, jsonCharacter)
    {
        var character = new Models.Characters.Character();

        character.setId(jsonCharacter.id);
        character.setName(jsonCharacter.name);
        character.setLevel(jsonCharacter.level);

        // setup guild if it exists

        var guildId = jsonCharacter.guildReference.id;

        if (guildId != undefined)
        {
            var guildDataModel = gameDataModel.getGuild(guildId);
            var guild = new Models.Characters.Guild();

            guild.setId(guildId);
            guild.setName(guildDataModel.name)

            var realmDataModel = gameDataModel.getRealm(guildDataModel.realmReference.id);

            var realm = new Models.Characters.Realm();
            realm.setId(realmDataModel.id);
            realm.setName(realmDataModel.name);

            guild.setRealm(realm);

            character.setGuild(guild);
        }

        // setup realm

        var realmDataModel = gameDataModel.getRealm(jsonCharacter.realmReference.id);

        var realm = new Models.Characters.Realm();
        realm.setId(realmDataModel.id);
        realm.setName(realmDataModel.name);

        character.setRealm(realm);

        // setup game class

        var gameClassDataModel = gameDataModel.getGameClass(jsonCharacter.gameClassReference.id);

        var gameClass = new Models.Characters.GameClass();
        gameClass.setId(gameClassDataModel.id);
        gameClass.setName(gameClassDataModel.name);

        character.setGameClass(gameClass);

        // setup game race

        var gameClassRaceModel = gameDataModel.getGameRace(jsonCharacter.gameRaceReference.id);

        var gameRace = new Models.Characters.GameRace();
        gameRace.setId(gameClassRaceModel.id);
        gameRace.setName(gameClassRaceModel.name);

        character.setGameRace(gameRace);

        // setup claims

        if ('claim' in jsonCharacter)
        {
            var claim = new Models.Characters.Claim();

            claim.setComment(jsonCharacter.claim.comment);
            claim.setRaider(jsonCharacter.claim.raider);
            claim.setRoles(jsonCharacter.claim.roles);

            character.setClaim(claim);
        }

        return character;
    }

    this.createCharacterArray = function(gameDataModel, jsonCharacters)
    {
        var characters = [];

        for (var i = 0; i < jsonCharacters.length; i++)
        {
            var jsonCharacter = jsonCharacters[i];

            characters.push(this.createSingleCharacter(gameDataModel, jsonCharacter));
        }

        return characters;
    }
}
