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
.controller('ViewEventCtrl', function($scope, $rootScope, $stateParams, eventService)
{
    var ctrl = this;

    ctrl.eventId = $stateParams.eventId;

    ctrl.eventDto = null;

    var promise = eventService.getEventById(ctrl.eventId);

    promise.then(
        function(eventDto)
        {
            try
            {
                ctrl.eventDto = eventDto;
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
});
