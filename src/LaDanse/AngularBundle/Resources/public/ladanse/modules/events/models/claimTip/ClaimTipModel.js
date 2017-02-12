/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

ClaimTip.ClaimTipModel = (function ()
{
    function ClaimTipModel()
    {
    }

    Object.defineProperty(ClaimTipModel.prototype, "name",
        {
            get: function ()
            {
                return this._name;
            },
            set: function (name)
            {
                this._name = name;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "realm",
        {
            get: function ()
            {
                return this._realm;
            },
            set: function (realm)
            {
                this._realm = realm;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "level",
        {
            get: function ()
            {
                return this._level;
            },
            set: function (level)
            {
                this._level = level;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "class",
        {
            get: function ()
            {
                return this._class;
            },
            set: function (pClass)
            {
                this._class = pClass;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "race",
        {
            get: function ()
            {
                return this._race;
            },
            set: function (race)
            {
                this._race = race;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "raider",
        {
            get: function ()
            {
                return this._raider;
            },
            set: function (raider)
            {
                this._raider = raider;
            },
            enumerable: true
        }
    );

    Object.defineProperty(ClaimTipModel.prototype, "roles",
        {
            get: function ()
            {
                return this._roles;
            },
            set: function (roles)
            {
                this._roles = roles;
            },
            enumerable: true
        }
    );

    ClaimTipModel.prototype.isForTank = function()
    {
        return this._isForRole("Tank");
    };

    ClaimTipModel.prototype.isForHealer = function()
    {
        return this._isForRole("Healer");
    };

    ClaimTipModel.prototype.isForDPS = function()
    {
        return this._isForRole("DPS");
    };

    ClaimTipModel.prototype._isForRole = function(roleName)
    {
        for(var i = 0; i < this._roles.length; i++)
        {
            if (this._roles[i] == roleName)
                return true;
        }

        return false;
    };

    return ClaimTipModel;
})();