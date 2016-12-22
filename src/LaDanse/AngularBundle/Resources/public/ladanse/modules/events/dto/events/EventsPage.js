DTO.Events.EventsPage = (function ()
{
    function EventsPage()
    {
    }

    Object.defineProperty(EventsPage.prototype, "events",
        {
            get: function ()
            {
                return this._events;
            },
            set: function (events)
            {
                this._events = events;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventsPage.prototype, "previousTimestamp",
        {
            get: function ()
            {
                return this._previousTimestamp;
            },
            set: function (previousTimestamp)
            {
                this._previousTimestamp = previousTimestamp;
            },
            enumerable: true
        }
    );

    Object.defineProperty(EventsPage.prototype, "nextTimestamp",
        {
            get: function ()
            {
                return this._nextTimestamp;
            },
            set: function (nextTimestamp)
            {
                this._nextTimestamp = nextTimestamp;
            },
            enumerable: true
        }
    );

    EventsPage.prototype.toJSON = function()
    {
        return {
            "events": this.events,
            "previousTimestamp": this.previousTimestamp,
            "nextTimestamp": this.nextTimestamp
        }
    };

    return EventsPage;
})();