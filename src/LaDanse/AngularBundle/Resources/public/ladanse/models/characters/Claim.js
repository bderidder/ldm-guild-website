/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

if(!Models.Characters) Models.Characters = {};

Models.Characters.Claim = function()
{
    this.raider;
    this.roles;
    this.comment;
    this.account;

    this.getRaider = function()
    {
        return this.raider;
    }

    this.setRaider = function(raider)
    {
        this.raider = raider;
    }

    this.getComment = function()
    {
        return this.comment;
    }

    this.setComment = function(comment)
    {
        this.comment = comment;
    }

    this.getAccount = function()
    {
        return this.account;
    }

    this.setAccount = function(account)
    {
        this.account = account;
    }

    this.getRoles = function()
    {
        return this.roles;
    }

    this.setRoles = function(roles)
    {
        this.roles = roles;
    }

    this.hasRole = function(role)
    {
        for (var i = 0; i < this.roles.length; i++)
        {
            if (this.roles[i] == role)
            {
                return true;
            }
        }

        return false;
    }
}
