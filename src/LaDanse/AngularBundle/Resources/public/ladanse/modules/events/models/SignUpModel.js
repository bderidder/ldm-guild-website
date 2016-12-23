/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var SignUpModel = (function ()
{
    function SignUpModel(signUpDto)
    {
        this._signUpDto = signUpDto;

        this._init();
    }

    Object.defineProperty(SignUpModel.prototype, "accountRef",
        {
            get: function ()
            {
                return this._signUpDto.accountRef;
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isForTank",
        {
            get: function ()
            {
                return this._checkForRole("Tank");
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isForHealer",
        {
            get: function ()
            {
                return this._checkForRole("Healer");
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isForDPS",
        {
            get: function ()
            {
                return this._checkForRole("DPS");
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isWillCome",
        {
            get: function ()
            {
                return this._signUpDto.type == "WillCome";
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isMightCome",
        {
            get: function ()
            {
                return this._signUpDto.type == "MightCome";
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isAbsence",
        {
            get: function ()
            {
                return this._signUpDto.type == "Absence";
            }
        }
    );

    Object.defineProperty(SignUpModel.prototype, "isForCurrentUser",
        {
            get: function ()
            {
                return (this._signUpDto.accountRef.id == currentAccount.id);
            }
        }
    );

    SignUpModel.prototype._checkForRole = function(roleName)
    {
        if (this._signUpDto.roles)
        {
            var roles = this._signUpDto.roles;

            var rolesCount = roles.length;
            for (var i = 0; i < rolesCount; i++)
            {
                if (roles[i] == roleName)
                    return true;
            }
        }

        return false;
    }

    SignUpModel.prototype._init = function()
    {

    }

    return SignUpModel;
})();