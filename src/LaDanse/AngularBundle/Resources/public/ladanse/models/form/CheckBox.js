Form.CheckBox = (function ()
{
    function CheckBox()
    {
        this._checked = false;
        this._disabled = false;
        this._error = false;
    }

    Object.defineProperty(CheckBox.prototype, "checked",
        {
            get: function ()
            {
                return this._checked;
            },
            set: function (checked)
            {
                this._checked = checked;
            }
        }
    );

    Object.defineProperty(CheckBox.prototype, "disabled",
        {
            get: function ()
            {
                return this._disabled;
            },
            set: function (disabled)
            {
                this._disabled = disabled;
            }
        }
    );

    Object.defineProperty(CheckBox.prototype, "error",
        {
            get: function ()
            {
                return this._error;
            },
            set: function (error)
            {
                this._error = error;
            }
        }
    );

    return CheckBox;
})();