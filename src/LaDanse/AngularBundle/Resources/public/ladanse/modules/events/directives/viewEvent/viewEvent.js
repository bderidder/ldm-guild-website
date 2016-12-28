/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('viewEvent', function()
{
    return {
        restrict: 'E',
        controller: 'ViewEventCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/viewEvent/viewEvent.html')
    };
})
.controller('ViewEventCtrl', function($scope, $rootScope, $stateParams, $state, eventService)
{
    var ctrl = this;

    ctrl.eventId = $stateParams.eventId;

    ctrl.event = null;

    ctrl.removeEventClicked = function(signUpId)
    {
        alertify.confirm(
            'Delete Event',
            'This will delete the event, are you sure?',
            function()
            {
                eventService.deleteEvent(ctrl.eventId)
                    .then(
                        function(eventDto)
                        {
                            alertify.success('Event deleted');

                            $state.go('events.calendar');
                        },
                        function(eventDto)
                        {
                            alertify.error('Failed to delete event');
                        }
                    );
            },
            function() {} // do nothing on cancel
        );
    };

    ctrl.editSignUpClicked = function(signUpId)
    {
    };

    ctrl.removeSignUpClicked = function(signUpId)
    {
        alertify.confirm(
            'Remove Sign Up',
            'This will remove your sign up, are you sure?',
            function()
            {
                eventService.deleteSignUp(ctrl.eventId, signUpId)
                    .then(
                        function(eventDto)
                        {
                            ctrl.setupEvent(eventDto);
                            alertify.success('Sign up deleted')
                        },
                        function(eventDto)
                        {
                            alertify.error('Failed to delete sign up')
                        }
                    );
            },
            function() {} // do nothing on cancel
        );
    };

    ctrl.accountLinkClicked = function(displayName)
    {
        var searchCriteria = new SearchCriteria();
        searchCriteria.setClaimingMember(displayName);

        var jsonAsString = JSON.stringify(searchCriteria);

        var base64Json = btoa(jsonAsString);

        document.location.href = '/app/roster#/roster?criteria=' + base64Json;
    };

    ctrl.setupEvent = function(eventDto)
    {
        ctrl.event = new EventModel(eventDto);
    };

    ctrl.init = function()
    {
        var promise = eventService.getEventById(ctrl.eventId);

        promise.then(
            function(eventDto)
            {
                try
                {
                    ctrl.setupEvent(eventDto)
                }
                catch(e)
                {
                    console.log(e);
                }
            },
            function(reason)
            {
                console.log('Failed to get event - ' + reason);
            }
        );
    };

    ctrl.init();
});
