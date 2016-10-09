/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.Character = function()
{
    this.id;
    this.name;
    this.level;
    this.guild;
    this.realm;
    this.gameRace;
    this.gameClass;

    this.getId = function()
    {
        return this.id;
    }

    this.setId = function(id)
    {
        this.id = id;
    }

    this.getName = function()
    {
        return this.name;
    }

    this.setName = function(name)
    {
        this.name = name;
    }

    this.getLevel = function()
    {
        return this.level;
    }

    this.setLevel = function(level)
    {
        this.level = level;
    }

    this.getGuild = function()
    {
        return this.guild;
    }

    this.setGuild = function(guild)
    {
        this.guild = guild;
    }

    this.getRealm = function()
    {
        return this.realm;
    }

    this.setRealm = function(realm)
    {
        this.realm = realm;
    }

    this.getGameClass = function()
    {
        return this.gameClass;
    }

    this.setGameClass = function(gameClass)
    {
        this.gameClass = gameClass;
    }

    this.getGameRace = function()
    {
        return this.gameRace;
    }

    this.setGameRace = function(gameRace)
    {
        this.gameRace = gameRace;
    }

    this.getClaim = function()
    {
        return this.claim;
    }

    this.setClaim = function(claim)
    {
        this.claim = claim;
    }
}
