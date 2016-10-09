/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.Guild = function()
{
    this.id;
    this.name;
    this.realm;

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

    this.getRealm = function()
    {
        return this.realm;
    }

    this.setRealm = function(realm)
    {
        this.realm = realm;
    }
}
