/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('createEvent', function()
{
    return {
        restrict: 'E',
        controller: 'CreateEventCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/createEvent/createEvent.html')
    };
})
.controller('CreateEventCtrl', function($scope, $rootScope, $stateParams, $state, eventService)
{
    var ctrl = this;

    ctrl.callback = {
        save: function()
        {
            ctrl.saveEvent();
        },
        cancel: function()
        {
            $state.go('events.calendar');
        }
    };

    ctrl.saveEvent = function()
    {
        var postEvent = new DTO.Events.PostEvent();

        postEvent.name = ctrl.editorModel.name;
        postEvent.description = ctrl.editorModel.description;

        postEvent.inviteTime = ctrl.editorModel.inviteTime;
        postEvent.startTime = ctrl.editorModel.startTime;
        postEvent.endTime = ctrl.editorModel.endTime;

        var idReference = new DTO.Shared.IdReference();
        idReference.id = currentAccount.id;

        postEvent.organiserReference = idReference;

        eventService.postEvent(postEvent)
            .then(
                function(eventDto)
                {
                    alertify.success('Created event');

                    $state.go('events.event.view', { eventId: eventDto.id });
                },
                function(errorMessage)
                {
                    alertify.error('Failed to create event');
                }
            );
    };

    ctrl.createDateForTime = function(hours, minutes)
    {
        var now = new Date();

        now.setHours(hours);
        now.setMinutes(minutes);
        now.setSeconds(0);
        now.setMilliseconds(0);

        return now;
    };

    ctrl.init = function()
    {
        ctrl.editorModel = new EventEditorModel();

        ctrl.editorModel.inviteTime = ctrl.createDateForTime(19, 15);
        ctrl.editorModel.startTime = ctrl.createDateForTime(19, 30);
        ctrl.editorModel.endTime = ctrl.createDateForTime(22, 0);
    };

    ctrl.init();
});
