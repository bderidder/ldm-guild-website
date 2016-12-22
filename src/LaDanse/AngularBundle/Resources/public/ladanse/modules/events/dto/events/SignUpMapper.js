"use strict";

DTO.Events.SignUpMapper =
{
    "mapSingleObject": function(signUpObject)
    {
        var signUpDto = new DTO.Events.SignUp();

        signUpDto.id = signUpObject.id;
        signUpDto.type = signUpObject.type;
        signUpDto.roles = signUpObject.roles;

        signUpDto.accountRef = DTO.Shared.AccountReferenceMapper.mapSingleObject(signUpObject.accountRef);

        return signUpDto;
    },

    "mapArray": function(signUpObjectArray)
    {
        var result = [];

        var arrayLength = signUpObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Events.SignUpMapper.mapSingleObject(signUpObjectArray[i]));
        }

        return result;
    }
};