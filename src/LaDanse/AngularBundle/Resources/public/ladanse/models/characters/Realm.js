/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.Realm = function()
{
    this.id;
    this.name;

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

    this.getNormalizedName = function()
    {
        var normalizedName = this.name.toLowerCase();

        normalizedName = normalizedName.replace(/ /g, "-");

        return normalizedName;
    }
}
