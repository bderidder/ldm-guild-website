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

            Post Sign Up
            Put Sign Up
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

        eventServiceInstance.deleteEvent = function(eventId)
        {
            var deferred = $q.defer();

            try
            {
                $http.delete(Routing.generate('deleteEvent', { eventId: eventId }))
                    .then(
                        function(httpRestResponse)
                        {
                            deferred.resolve();
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to delete event');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        eventServiceInstance.confirmEvent = function(eventId)
        {
            return this.updateEventState(eventId, "Confirmed");
        };

        eventServiceInstance.cancelEvent = function(eventId)
        {
            return this.updateEventState(eventId, "Cancelled");
        };

        eventServiceInstance.updateEventState = function(eventId, newState)
        {
            var deferred = $q.defer();

            var putEventState = new DTO.Events.PutEventState();
            putEventState.state = newState;

            try
            {
                $http.put(Routing.generate('putEventState', { eventId: eventId }), putEventState)
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to update event state to - ' + newState);
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

        eventServiceInstance.listClaims = function(eventId, accountId)
        {
            var deferred = $q.defer();

            try
            {
                $http.get(Routing.generate('listClaims', { eventId: eventId, accountId: accountId }))
                    .then(
                        function(httpRestResponse)
                        {
                            deferred.resolve(httpRestResponse.data);
                        },
                        function(httpRestResponse)
                        {
                            deferred.reject('Failed to load claims');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
                deferred.reject('Failed to load claims');
            }

            return deferred.promise;
        };

        return eventServiceInstance;
    });