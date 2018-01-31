"use strict";

DTO.Events.EventMapper =
{
    "mapSingleObject": function(eventObject)
    {
        var eventDto = new DTO.Events.Event();

        eventDto.id = eventObject.id;
        eventDto.name = eventObject.name;
        eventDto.description = eventObject.description;
        eventDto.inviteTime = moment(eventObject.inviteTime);
        eventDto.startTime = moment(eventObject.startTime);
        eventDto.endTime = moment(eventObject.endTime);
        eventDto.state = eventObject.state;
        eventDto.commentGroupRef = DTO.Shared.IdReferenceMapper.mapSingleObject(eventObject.commentGroupRef);
        eventDto.organiserRef = DTO.Shared.AccountReferenceMapper.mapSingleObject(eventObject.organiserRef);
        eventDto.signUps = DTO.Events.SignUpMapper.mapArray(eventObject.signUps);

        return eventDto;
    },

    "mapArray": function(eventObjectArray)
    {
        var result = [];

        if (!eventObjectArray)
        {
            console.log("eventObjectArray was null, that should not happen");

            return result;
        }

        var arrayLength = eventObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Events.EventMapper.mapSingleObject(eventObjectArray[i]));
        }

        return result;
    }
};