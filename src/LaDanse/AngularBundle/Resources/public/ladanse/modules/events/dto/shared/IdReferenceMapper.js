"use strict";

DTO.Shared.IdReferenceMapper =
{
    "mapSingleObject": function(idRefObject)
    {
        if (idRefObject == undefined || idRefObject == null)
        {
            return null;
        }

        var idReference = new DTO.Shared.IdReference();

        idReference.id = idRefObject.id;
        idReference.name = idRefObject.name;

        return idReference;
    },

    "mapArray": function(idRefObjectArray)
    {
        if (idRefObjectArray == undefined || idRefObjectArray == null)
        {
            return null;
        }

        var result = [];

        var arrayLength = idRefObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Shared.IdReferenceMapper.mapSingleObject(idRefObjectArray[i]));
        }

        return result;
    }
};