DTO.Events.SignUp = (function ()
{
    function SignUp() {
        this._id = null;
        this._accountRef = null;
        this._type = null;
        this._roles = null;
    }

    Object.defineProperty(SignUp.prototype, "id",
        {
            get: function ()
            {
                return this._id;
            },
            set: function (id)
            {
                this._id = id;
            }
        }
    );

    Object.defineProperty(SignUp.prototype, "accountRef",
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

    Object.defineProperty(SignUp.prototype, "type",
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

    Object.defineProperty(SignUp.prototype, "roles",
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

    SignUp.prototype.toJSON = function()
    {
        return {
            "id": this.id,
            "type": this.type,
            "roles": this.roles,
            "accountRef": this.accountRef
        }
    };

    return SignUp;
})();