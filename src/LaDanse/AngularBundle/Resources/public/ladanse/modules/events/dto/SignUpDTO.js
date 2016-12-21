var SignUpDTO = (function ()
{
    function SignUpDTO() {
        this._id = null;
        this._accountRef = null;
        this._type = null;
        this._roles = null;
    }

    Object.defineProperty(SignUpDTO.prototype, "id",
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

    Object.defineProperty(SignUpDTO.prototype, "accountRef",
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

    Object.defineProperty(SignUpDTO.prototype, "type",
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

    Object.defineProperty(SignUpDTO.prototype, "roles",
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

    return SignUpDTO;
})();