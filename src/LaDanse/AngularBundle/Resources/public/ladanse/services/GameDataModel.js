/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

function GameDataModel(guilds, realms, gameRaces, gameClasses, gameFactions)
{
    this.guilds = guilds;
    this.realms = realms;
    this.gameClasses = gameClasses;
    this.gameRaces = gameRaces;
    this.gameFactions = gameFactions;

    this.getGuilds = function()
    {
        return this.guilds;
    };

    this.getGuild = function(guildId)
    {
        for (var i = 0; i < this.guilds.length; i++)
        {
            if (this.guilds[i].id == guildId)
            {
                return this.guilds[i]
            }
        }

        return null;
    };

    this.getRealms = function()
    {
        return this.realms;
    };

    this.getRealm = function(realmId)
    {
        for (var i = 0; i < this.realms.length; i++)
        {
            if (this.realms[i].id == realmId)
            {
                return this.realms[i]
            }
        }

        return null;
    };

    this.getGameClasses = function()
    {
        return this.gameClasses;
    };

    this.getGameClass = function(gameClassId)
    {
        for (var i = 0; i < this.gameClasses.length; i++)
        {
            if (this.gameClasses[i].id == gameClassId)
            {
                return this.gameClasses[i]
            }
        }

        return null;
    };

    this.getGameRaces = function()
    {
        return this.gameRaces;
    };

    this.getGameRace = function(gameRaceId)
    {
        for (var i = 0; i < this.gameRaces.length; i++)
        {
            if (this.gameRaces[i].id == gameRaceId)
            {
                return this.gameRaces[i]
            }
        }

        return null;
    };

    this.getGameFactions = function()
    {
        return this.gameFactions;
    };

    this.getGameFaction = function(gameFactionId)
    {
        for (var i = 0; i < this.gameFactions.length; i++)
        {
            if (this.gameFactions[i].id == gameFactionId)
            {
                return this.gameFactions[i]
            }
        }

        return null;
    };
}
