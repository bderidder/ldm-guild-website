/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.Account = function()
{
    this.id;
    this.displayName;

    this.getId = function()
    {
        return this.id;
    }

    this.setId = function(id)
    {
        this.id = id;
    }

    this.getDisplayName = function()
    {
        return this.displayName;
    }

    this.setDisplayName = function(displayName)
    {
        this.displayName = displayName;
    }
}
