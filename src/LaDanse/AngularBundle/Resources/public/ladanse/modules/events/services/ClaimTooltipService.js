"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'claimTooltipService',
    function($http, $log, $q, $compile)
    {
        var claimTooltipServiceInstance = {};

        var characterClaimsCache = new CharacterClaimsCache();
        var eventCache = new EventCache();

        function getSomeString()
        {
            return 'Hello World';
        }

        /*

            GET event details (CACHE)
                if not in cache, download and store in cache
            GET character Claims (CACHE)
                if not in cache, download and store in cache

            Find signUp for accountId and corresponding roles

            Find claims that have at least one role, add found claim to model

            Combine template and model into tooltip HTML

         */

        claimTooltipServiceInstance.getTooltipHTML = function(scope, eventId, accountId)
        {
            var deferred = $q.defer();

            var templateUrl = Assetic.generate('/ladanseangular/ladanse/modules/events/directives/qtipClaim/claimTooltip.html');

            console.log("getTooltipHTML");

            try
            {
                $http.get(templateUrl)
                    .then(
                        function (httpRestResponse)
                        {
                            console.log("getTooltipHTML - get content");

                            var claimScope = scope.$new(true);
                            claimScope.name = getSomeString();

                            var content = $compile(httpRestResponse.data)(claimScope);

                            deferred.resolve(content);
                        },
                        function (httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to get events');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        return claimTooltipServiceInstance;
    });