"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'eventService',
    function($http, $log, $q)
    {
        var eventServiceInstance = {};

        /*
            Put Event
            Put Event State

            Post Sign Up
            Put Sign Up
         */

        eventServiceInstance.getEventsPage = function(startOn)
        {
            if (!startOn || startOn == null)
            {
                startOn = moment().format('YYYYMMDD');
            }
            else
            {
                startOn = moment(startOn).format('YYYYMMDD');
            }

            console.log("Fetching events page for " + startOn);

            var deferred = $q.defer();

            try
            {
                $http.get(Routing.generate('queryEvents', { 'startOn': startOn}))
                    .then(
                        function (httpRestResponse)
                        {
                            var eventsPageDto = DTO.Events.EventsPageMapper.mapSingleObject(httpRestResponse.data);

                            console.log("Got events page DTO");
                            console.log(eventsPageDto);

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

        eventServiceInstance.postEvent = function(postEvent)
        {
            var deferred = $q.defer();

            try
            {
                $http.post(Routing.generate('postEvent'), postEvent)
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to create event');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        eventServiceInstance.putEvent = function(eventId, putEvent)
        {
            var deferred = $q.defer();

            try
            {
                $http.put(Routing.generate('putEvent', { eventId: eventId }), putEvent)
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to update event');
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

        eventServiceInstance.postSignUp = function(eventId, postSignUp)
        {
            var deferred = $q.defer();

            try
            {
                $http.post(Routing.generate('postSignUp', { eventId: eventId }), postSignUp)
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to post sign up');
                        }
                    );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        eventServiceInstance.putSignUp = function(eventId, signUpId, putSignUp)
        {
            var deferred = $q.defer();

            try
            {
                $http.put(Routing.generate('putSignUp', { eventId: eventId, signUpId: signUpId }), putSignUp)
                    .then(
                        function(httpRestResponse)
                        {
                            var eventDto = DTO.Events.EventMapper.mapSingleObject(httpRestResponse.data);

                            deferred.resolve(eventDto);
                        },
                        function(httpRestResponse)
                        {
                            console.log(httpRestResponse.data);
                            deferred.reject('Failed to put sign up');
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