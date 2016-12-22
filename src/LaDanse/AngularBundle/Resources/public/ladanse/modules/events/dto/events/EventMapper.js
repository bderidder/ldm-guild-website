"use strict";

DTO.Events.EventMapper =
{
    "mapSingleObject": function(eventObject)
    {
        var eventDto = new DTO.Events.Event();

        eventDto.id = eventObject.id;
        eventDto.name = eventObject.name;
        eventDto.description = eventObject.description;
        eventDto.inviteTime = new Date(eventObject.inviteTime);
        eventDto.startTime = new Date(eventObject.startTime);
        eventDto.endTime = new Date(eventObject.endTime);
        eventDto.state = eventObject.state;
        eventDto.commentGroupRef = DTO.Shared.IdReferenceMapper.mapSingleObject(eventObject.commentGroupRef);
        eventDto.organiserRef = DTO.Shared.AccountReferenceMapper.mapSingleObject(eventObject.organiserRef);
        eventDto.signUps = DTO.Events.SignUpMapper.mapArray(eventObject.signUps);

        return eventDto;
    },

    "mapArray": function(eventObjectArray)
    {
        var result = [];

        var arrayLength = eventObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Events.EventMapper.mapSingleObject(eventObjectArray[i]));
        }

        return result;
    }
};