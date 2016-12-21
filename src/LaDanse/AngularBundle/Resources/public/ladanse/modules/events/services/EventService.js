"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'eventService',
    function($http, $log, $q)
    {
        var eventServiceInstance = {};

        /*
            Get Events (paged)
            Get Event by Id

            Post Event
            Put Event
            Put Event State
            Delete Event

            Post Sign Up
            Put Sign Up
            Delete Sign Up
         */

        eventServiceInstance.getEventById = function(eventId)
        {
            var deferred = $q.defer();

            $http.get(Routing.generate('queryEventById', { eventId: eventId }))
                .then(
                    function(httpRestResponse)
                    {
                        deferred.resolve(httpRestResponse.data);
                    },
                    function(httpRestResponse)
                    {
                        reject('Failed to get events');
                    }
                );

            return deferred.promise;
        };

        return eventServiceInstance;
    });