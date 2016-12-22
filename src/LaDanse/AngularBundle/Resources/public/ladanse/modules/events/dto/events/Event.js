DTO.Events.Event = (function ()
{
    function Event() {
        this._id = null;
        this._name = null;
        this._description = null;
        this._organiser = null;
        this._inviteTime = null;
        this._startTime = null;
        this._endTime = null;
        this._state = null;
        this._commentGroup = null;
        this._signUps = null;
    }

    Object.defineProperty(Event.prototype, "id",
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

    Object.defineProperty(Event.prototype, "name",
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

    Object.defineProperty(Event.prototype, "description",
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

    Object.defineProperty(Event.prototype, "organiserRef",
        {
            get: function ()
            {
                return this._organiserRef;
            },
            set: function (organiserRef)
            {
                this._organiserRef = organiserRef;
            },
            enumerable: true
        }
    );

    Object.defineProperty(Event.prototype, "inviteTime",
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

    Object.defineProperty(Event.prototype, "startTime",
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

    Object.defineProperty(Event.prototype, "endTime",
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

    Object.defineProperty(Event.prototype, "state",
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

    Object.defineProperty(Event.prototype, "commentGroupRef",
        {
            get: function ()
            {
                return this._commentGroupRef;
            },
            set: function (commentGroupRef)
            {
                this._commentGroupRef = commentGroupRef;
            },
            enumerable: true
        }
    );

    Object.defineProperty(Event.prototype, "signUps",
        {
            get: function ()
            {
                return this._signUps;
            },
            set: function (signUps)
            {
                this._signUps = signUps;
            },
            enumerable: true
        }
    );

    Event.prototype.toJSON = function()
    {
        return {
            "id": this.id,
            "name": this.name,
            "description": this.description,
            "inviteTime": this.inviteTime.toISOString(),
            "startTime": this.startTime.toISOString(),
            "endTime": this.endTime.toISOString(),
            "state": this.state,
            "commentGroupRef": this.commentGroupRef,
            "organiserRef": this.organiserRef,
            "signUps": this.signUps
        }
    };

    return Event;
})();