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
    var ON_DATE_FORMAT = 'YYYYMMDD';

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
                    alertify.error('Failed to create event - ' + errorMessage);
                }
            );
    };

    ctrl.init = function()
    {
        ctrl.editorModel = new EventEditorModel();

        // Create a new moment based on the given date.
        // We don't really care about the timezone set, we will update it immediately.
        var baseDate = moment($stateParams.onDate, ON_DATE_FORMAT);

        if (!baseDate.isValid())
        {
            $state.go('events.calendar');
        }

        ctrl.editorModel.inviteTime = TimeUtils.createDefaultInviteTime(baseDate);
        ctrl.editorModel.startTime = TimeUtils.createDefaultStartTime(baseDate);
        ctrl.editorModel.endTime = TimeUtils.createDefaultEndTime(baseDate);
    };

    ctrl.init();
});
