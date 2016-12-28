DTO.Events.PutEventState = (function ()
{
    function PutEventState()
    {
        this._state = null;
    }

    Object.defineProperty(PutEventState.prototype, "state",
        {
            get: function ()
            {
                return this._state;
            },
            set: function (state)
            {
                this._state = state;
            },
            enumerable: true
        }
    );

    PutEventState.prototype.toJSON = function()
    {
        return {
            "state": this.state
        }
    };

    return PutEventState;
})();