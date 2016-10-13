/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

function SearchCriteria()
{
    this.name = "";
    this.minLevel = 1;
    this.maxLevel = 110;
    this.raider = 1;
    this.claimed = 1;
    this.guild = null;
    this.gameRace = null;
    this.gameClass = null;
    this.gameFaction = null;
    this.roles = null;

    this.setName = function(name)
    {
        this.name = name;
    }

    this.getName = function()
    {
        return this.name;
    }

    this.setMinLevel = function(minLevel)
    {
        this.minLevel = minLevel;
    }

    this.getMinLevel = function()
    {
        return this.minLevel;
    }

    this.setMaxLevel = function(maxLevel)
    {
        this.maxLevel = maxLevel;
    }

    this.getMaxLevel = function()
    {
        return this.maxLevel;
    }

    this.setRaider = function(raider)
    {
        this.raider = raider;
    }

    this.getRaider = function()
    {
        return this.raider;
    }

    this.setClaimed= function(claimed)
    {
        this.claimed = claimed;
    }

    this.getClaimed = function()
    {
        return this.claimed;
    }

    this.setGuild = function(guild)
    {
        this.guild = guild;
    }

    this.getGuild = function()
    {
        return this.guild;
    }

    this.setGameRace = function(gameRace)
    {
        this.gameRace = gameRace;
    }

    this.getGameRace = function()
    {
        return this.gameRace;
    }

    this.setGameClass = function(gameClass)
    {
        this.gameClass = gameClass;
    }

    this.getGameClass = function()
    {
        return this.gameClass;
    }

    this.setGameFaction = function(gameFaction)
    {
        this.gameFaction = gameFaction;
    }

    this.getGameFaction = function()
    {
        return this.gameFaction;
    }

    this.setRoles = function(roles)
    {
        this.roles = roles;
    }

    this.getRoles = function()
    {
        return this.roles;
    }
}
