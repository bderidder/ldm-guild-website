DTO.Events.PutSignUp = (function ()
{
    function PutSignUp()
    {
        this._accountRef = null;
        this._type = null;
        this._roles = null;
    }

    /*
     * Must be an instance of DTO.Shared.IdReference
     */
    Object.defineProperty(PutSignUp.prototype, "accountRef",
        {
            get: function ()
            {
                return this._accountRef;
            },
            set: function (accountRef)
            {
                this._accountRef = accountRef;
            }
        }
    );

    Object.defineProperty(PutSignUp.prototype, "type",
        {
            get: function ()
            {
                return this._type;
            },
            set: function (type)
            {
                this._type = type;
            }
        }
    );

    Object.defineProperty(PutSignUp.prototype, "roles",
        {
            get: function ()
            {
                return this._roles;
            },
            set: function (roles)
            {
                this._roles = roles;
            }
        }
    );

    PutSignUp.prototype.toJSON = function()
    {
        return {
            "signUpType": this.type,
            "roles": this.roles,
            "accountRef": this.accountRef
        }
    };

    return PutSignUp;
})();