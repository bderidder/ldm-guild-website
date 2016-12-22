DTO.Shared.AccountReference = (function ()
{
    function AccountReference()
    {
        this._id = -1;
    }

    Object.defineProperty(AccountReference.prototype, "id",
        {
            get: function ()
            {
                return this._id;
            },
            set: function (id)
            {
                this._id = id;
            },
            enumerable: true
        }
    );

    Object.defineProperty(AccountReference.prototype, "name",
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

    AccountReference.prototype.toJSON = function()
    {
        return {
            "id": this.id,
            "name": this.name
        }
    };

    return AccountReference;
})();