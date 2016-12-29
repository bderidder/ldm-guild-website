DTO.Events.PostSignUp = (function ()
{
    function PostSignUp()
    {
        this._accountRef = null;
        this._type = null;
        this._roles = null;
    }

    /*
     * Must be an instance of DTO.Shared.IdReference
     */
    Object.defineProperty(PostSignUp.prototype, "accountRef",
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

    Object.defineProperty(PostSignUp.prototype, "type",
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

    Object.defineProperty(PostSignUp.prototype, "roles",
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

    PostSignUp.prototype.toJSON = function()
    {
        return {
            "signUpType": this.type,
            "roles": this.roles,
            "accountReference": this.accountRef
        }
    };

    return PostSignUp;
})();