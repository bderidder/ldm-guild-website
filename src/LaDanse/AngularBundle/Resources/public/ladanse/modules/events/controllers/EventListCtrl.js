/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

(function()
{
    "use strict";

    GetAngularModule('Events')
        .controller('EventListController', EventListController);

    EventListController.$inject = ['$scope', 'eventService'];

    function EventListController($scope, eventService)
    {
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

                    $scope.eventList = eventList;
                },
                function(errStr)
                {
                    console.log(errStr);
                }
            );
    }

})();
