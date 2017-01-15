/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('calendar', function()
{
    return {
        restrict: 'E',
        controller: 'CalendarCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/calendar/calendar.html')
    };
})
.controller('CalendarCtrl', function($scope, $rootScope, eventService)
{
    var ctrl = this;

    ctrl.eventsPage = null;

    ctrl.raidWeekOne = new Calendar.RaidWeekModel(new Date(2017, 0, 17));

    var promise = eventService.getEventsPage();

    promise.then(
        function(eventsPage)
        {
            try
            {
                ctrl.eventsPage = eventsPage;

                var previousTimestamp = moment(eventsPage.previousTimestamp).format('YYYYMMDD');
                var nextTimestamp = moment(eventsPage.nextTimestamp).format('YYYYMMDD');
            }
            catch(e)
            {
                console.log(e);
            }
        },
        function(reason)
        {
            console.log('Failed to get events - ' + reason);
        }
    );
});
