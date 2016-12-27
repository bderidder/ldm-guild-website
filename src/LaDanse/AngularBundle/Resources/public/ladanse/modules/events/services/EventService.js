"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'eventService',
    function($http, $log, $q)
    {
        var eventServiceInstance = {};

        /*
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

        eventServiceInstance.deleteSignUp = function(eventId, signUpId)
        {
            var deferred = $q.defer();

            try
            {
                $http.delete(Routing.generate('deleteSignUp', { eventId: eventId, signUpId: signUpId }))
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to delete given sign up');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
                deferred.reject('Failed to delete given sign up');
            }

            return deferred.promise;
        };

        return eventServiceInstance;
    });