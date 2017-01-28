"use strict";

DTO.Events.EventsPageMapper =
{
    "mapSingleObject": function(eventsPageObject)
    {
        var eventsPageDto = new DTO.Events.EventsPage();

        eventsPageDto.events = DTO.Events.EventMapper.mapArray(eventsPageObject.events);
        eventsPageDto.previousTimestamp = moment(eventsPageObject.previousTimestamp);
        eventsPageDto.nextTimestamp = moment(eventsPageObject.nextTimestamp);

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