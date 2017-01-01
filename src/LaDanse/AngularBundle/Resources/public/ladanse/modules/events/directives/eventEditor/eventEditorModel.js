var EventEditorModel = (function ()
{
    function EventEditorModel()
    {
        this._name = null;
        this._description = null;
        this._inviteTime = null;
        this._startTime = null;
        this._endTime = null;
    }

    Object.defineProperty(EventEditorModel.prototype, "name",
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

    Object.defineProperty(EventEditorModel.prototype, "description",
        {
            get: function ()
            {
                return this._description;
            },
            set: function (description)
            {
                this._description = description;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventEditorModel.prototype, "inviteTime",
        {
            get: function ()
            {
                return this._inviteTime;
            },
            set: function (inviteTime)
            {
                this._inviteTime = inviteTime;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventEditorModel.prototype, "startTime",
        {
            get: function ()
            {
                return this._startTime;
            },
            set: function (startTime)
            {
                this._startTime = startTime;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventEditorModel.prototype, "endTime",
        {
            get: function ()
            {
                return this._endTime;
            },
            set: function (endTime)
            {
                this._endTime = endTime;
            },
            enumerable: true
        }
    );

    return EventEditorModel;
})();