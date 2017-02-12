"use strict";

var charactersModule = GetAngularModule(CHARACTERS_MODULE_NAME);

charactersModule.service(
    'characterService',
    function($http, $log, $q)
    {
        var characterServiceInstance = {};

        characterServiceInstance.getCharactersClaimedByAccount = function(accountId)
        {
            var deferred = $q.defer();

            try
            {
                $http.get(Routing.generate('getCharactersClaimedByAccount', { accountId: accountId }))
                    .then(
                        function(httpRestResponse)
                        {
                            deferred.resolve(httpRestResponse.data);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to get claims for account ' + accountId);
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        return characterServiceInstance;
    });