var SignUpEditorModel = (function ()
{
    function SignUpEditorModel()
    {
        this._type = null;
        this._roles = null;
    }

    Object.defineProperty(SignUpEditorModel.prototype, "type",
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

    Object.defineProperty(SignUpEditorModel.prototype, "roles",
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

    return SignUpEditorModel;
})();