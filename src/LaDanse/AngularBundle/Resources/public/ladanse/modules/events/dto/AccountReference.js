var AccountReference = (function ()
{
    function AccountReference() {
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

    Object.defineProperty(AccountReference.prototype, "displayName",
        {
            get: function ()
            {
                return this._displayName;
            },
            set: function (displayName)
            {
                this._displayName = displayName;
            },
            enumerable: true
        }
    );

    return AccountReference;
})();