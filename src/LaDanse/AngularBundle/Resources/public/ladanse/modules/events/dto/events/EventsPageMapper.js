"use strict";

DTO.Events.EventsPageMapper =
{
    "mapSingleObject": function(eventsPageObject)
    {
        var eventsPageDto = new DTO.Events.EventsPage();

        eventsPageDto.events = eventsPageObject.events;
        eventsPageDto.previousTimestamp = new Date(eventsPageObject.previousTimestamp);
        eventsPageDto.nextTimestamp = new Date(eventsPageObject.nextTimestamp);

        return eventsPageDto;
    },

    "mapArray": function(eventsPageObjectArray)
    {
        var result = [];

        var arrayLength = eventsPageObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Events.EventsPageMapper.mapSingleObject(eventsPageObjectArray[i]));
        }

        return result;
    }
};