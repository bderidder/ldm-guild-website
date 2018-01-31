/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

(function()
{
    "use strict";

    GetAngularModule('Events')
        .directive('eventListTile', function()
        {
            return {
                restrict: 'E',
                controller: 'EventListController',
                controllerAs: 'ctrl',
                scope: {},
                templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/eventListTile/EventListTile.html')
            };
        })
        .controller('EventListController', EventListController);

    EventListController.$inject = ['eventService'];

    function EventListController(eventService)
    {
        var ctrl = this;

        console.log("EventListTile.js - calling getEventsPage");

        eventService.getEventsPage(moment())
            .then(
                function(eventsPageDto)
                {
                    var events = eventsPageDto.events;

                    var eventList = [];

                    var maxCount = events.length > 4 ? 4 : events.length;

                    for(var i = 0; i < maxCount; i++)
                    {
                        eventList.push(new EventModel(events[i]));
                    }

                    ctrl.eventList = eventList;
                },
                function(errStr)
                {
                    console.log(errStr);
                }
            );
    }

})();
