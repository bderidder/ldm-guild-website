"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'eventService',
    function($http, $log, $q)
    {
        var eventServiceInstance = {};

        /*
            Get Events (paged)

            Post Event
            Put Event
            Put Event State
            Delete Event

            Post Sign Up
            Put Sign Up
            Delete Sign Up
         */

        eventServiceInstance.getEventsPage = function(startOn)
        {
            if (startOn == undefined || startOn == null)
            {
                startOn = moment().format('YYYYMMDD');
            }

            var deferred = $q.defer();

            try
            {
                $http.get(Routing.generate('queryEvents', { 'startOn': startOn}))
                    .then(
                        function (httpRestResponse)
                        {
                            var eventsPageDto = DTO.Events.EventsPageMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventsPageDto);
                        },
                        function (httpRestResponse)
                        {
                            reject('Failed to get events');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        eventServiceInstance.getEventById = function(eventId)
        {
            var deferred = $q.defer();

            try
            {
                $http.get(Routing.generate('queryEventById', { eventId: eventId }))
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            reject('Failed to get events');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        return eventServiceInstance;
    });