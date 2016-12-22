"use strict";

DTO.Shared.AccountReferenceMapper =
{
    "mapSingleObject": function(accountRefObject)
    {
        if (accountRefObject == undefined || accountRefObject == null)
        {
            return null;
        }

        var accountReference = new DTO.Shared.AccountReference();

        accountReference.id = accountRefObject.id;
        accountReference.name = accountRefObject.name;

        return accountReference;
    },

    "mapArray": function(signUpObjectArray)
    {
        if (signUpObjectArray == undefined || signUpObjectArray == null)
        {
            return null;
        }

        var result = [];

        var arrayLength = signUpObjectArray.length;
        for (var i = 0; i < arrayLength; i++)
        {
            result.push(DTO.Shared.AccountReferenceMapper.mapSingleObject(signUpObjectArray[i]));
        }

        return result;
    }
};