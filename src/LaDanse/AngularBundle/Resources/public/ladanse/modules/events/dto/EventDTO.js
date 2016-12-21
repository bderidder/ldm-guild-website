var EventDTO = (function ()
{
    function EventDTO() {
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

    Object.defineProperty(EventDTO.prototype, "id",
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

    Object.defineProperty(EventDTO.prototype, "name",
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

    Object.defineProperty(EventDTO.prototype, "description",
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

    Object.defineProperty(EventDTO.prototype, "organiser",
        {
            get: function ()
            {
                return this._organiser;
            },
            set: function (organiser)
            {
                this._organiser = organiser;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventDTO.prototype, "inviteTime",
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

    Object.defineProperty(EventDTO.prototype, "startTime",
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

    Object.defineProperty(EventDTO.prototype, "endTime",
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

    Object.defineProperty(EventDTO.prototype, "state",
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

    Object.defineProperty(EventDTO.prototype, "commentGroup",
        {
            get: function ()
            {
                return this._commentGroup;
            },
            set: function (commentGroup)
            {
                this._commentGroup = commentGroup;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventDTO.prototype, "signUps",
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

    return EventDTO;
})();