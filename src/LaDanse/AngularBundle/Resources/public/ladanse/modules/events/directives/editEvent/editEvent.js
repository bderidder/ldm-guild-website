/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('editEvent', function()
{
    return {
        restrict: 'E',
        controller: 'EditEventCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/editEvent/editEvent.html')
    };
})
.controller('EditEventCtrl', function($scope, $rootScope, $stateParams, $state, eventService)
{
    var ctrl = this;

    ctrl.eventId = $stateParams.eventId;
    ctrl.event = null;

    ctrl.callback = {
        save: function()
        {
            ctrl.updateEvent();
        },
        cancel: function()
        {
            $state.go('events.event.view', { eventId: ctrl.eventId });
        }
    };

    ctrl.updateEvent = function()
    {
        var putEvent = new DTO.Events.PutEvent();

        putEvent.name = ctrl.editorModel.name;
        putEvent.description = ctrl.editorModel.description;

        putEvent.inviteTime = ctrl.editorModel.inviteTime;
        putEvent.startTime = ctrl.editorModel.startTime;
        putEvent.endTime = ctrl.editorModel.endTime;

        var idReference = new DTO.Shared.IdReference();
        idReference.id = ctrl.event.organiserRef.id;

        putEvent.organiserReference = idReference;

        eventService.putEvent(ctrl.eventId, putEvent)
            .then(
                function(eventDto)
                {
                    alertify.success('Updated event');

                    $state.go('events.event.view', { eventId: eventDto.id });
                },
                function(errorMessage)
                {
                    alertify.error('Failed to update event');
                }
            );
    };

    ctrl.setupEventEditorModel = function(eventDto)
    {
        ctrl.event = eventDto;

        ctrl.editorModel = new EventEditorModel();

        ctrl.editorModel.name = eventDto.name;
        ctrl.editorModel.description = eventDto.description;
        ctrl.editorModel.inviteTime = eventDto.inviteTime;
        ctrl.editorModel.startTime = eventDto.startTime;
        ctrl.editorModel.endTime = eventDto.endTime;
    };

    ctrl.init = function()
    {
        var promise = eventService.getEventById(ctrl.eventId);

        promise.then(
            function(eventDto)
            {
                try
                {
                    ctrl.setupEventEditorModel(eventDto)
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
