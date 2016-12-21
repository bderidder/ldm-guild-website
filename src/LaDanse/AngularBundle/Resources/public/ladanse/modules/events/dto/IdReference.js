var IdReference = (function ()
{
    function IdReference() {
        this._id = -1;
    }

    Object.defineProperty(IdReference.prototype, "id",
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

    return IdReference;
})();