/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.GameRace = function()
{
    this.id;
    this.name;
    this.gameFaction;

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

    this.getGameFaction = function()
    {
        return this.gameFaction;
    }

    this.setGameFaction = function(gameFaction)
    {
        this.gameFaction = gameFaction;
    }
}
