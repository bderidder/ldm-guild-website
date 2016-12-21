"use strict";

var EventDTOMapper = (function ()
{
    function EventDTOMapper()
    {
        this._test = "Hello World";
    }

    EventDTOMapper.prototype.singleObject = function(restData)
    {
        console.log('EventDTOMapper - ' + JSON.stringify(restData));

        var eventDto = new EventDTO();

        eventDto.id = restData.id;
        eventDto.name = restData.name;
        eventDto.description = restData.description;
        eventDto.inviteTime = restData.inviteTime;
        eventDto.startTime = restData.startTime;
        eventDto.endTime = restData.endTime;
        eventDto.state = restData.state;

        return eventDto;
    };

    return EventDTOMapper;
})();