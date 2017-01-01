DTO.Events.PutEvent = (function ()
{
    function PutEvent()
    {
    }

    Object.defineProperty(PutEvent.prototype, "name",
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

    Object.defineProperty(PutEvent.prototype, "description",
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

    Object.defineProperty(PutEvent.prototype, "organiserReference",
        {
            get: function ()
            {
                return this._organiserReference;
            },
            set: function (organiserReference)
            {
                this._organiserReference = organiserReference;
            },
            enumerable: true
        }
    );

    Object.defineProperty(PutEvent.prototype, "inviteTime",
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

    Object.defineProperty(PutEvent.prototype, "startTime",
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

    Object.defineProperty(PutEvent.prototype, "endTime",
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

    PutEvent.prototype.toJSON = function()
    {
        return {
            "name": this.name,
            "description": this.description,
            "inviteTime": moment(this.inviteTime).format("YYYY-MM-DDTHH:mm:ssZZ"),
            "startTime": moment(this.startTime).format("YYYY-MM-DDTHH:mm:ssZZ"),
            "endTime": moment(this.endTime).format("YYYY-MM-DDTHH:mm:ssZZ"),
            "organiserReference": this.organiserReference
        }
    };

    return PutEvent;
})();