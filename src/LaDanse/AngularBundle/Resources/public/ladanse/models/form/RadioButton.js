Form.RadioButton = (function ()
{
    function RadioButton()
    {
        this._checked = false;
        this._disabled = false;
        this._error = false;
    }

    Object.defineProperty(RadioButton.prototype, "value",
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

    Object.defineProperty(RadioButton.prototype, "disabled",
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

    Object.defineProperty(RadioButton.prototype, "error",
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

    return RadioButton;
})();