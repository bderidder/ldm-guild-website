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
.controller('ViewEventCtrl', function($scope, $rootScope, eventService)
{
    var ctrl = this;

    var accountReference = new AccountReference();

    accountReference.id = 15;
    accountReference.displayName = "Leto";

    var promise = eventService.getEventById(1);

    promise.then(
        function(event)
        {
            console.log('Got event - ' + event);

            var eventDtoMapper = new EventDTOMapper();

            var eventDto = eventDtoMapper.singleObject(event);

            console.log('eventDto - ' + eventDto.name);
            console.log('eventDto - ' + JSON.stringify(eventDto, Object.keys(eventDto.constructor.prototype)));
        },
        function(reason)
        {
            console.log('Failed to get event - ' + reason);
        }
    );
});
