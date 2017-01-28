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
.controller('CalendarCtrl', function($scope, $rootScope, $stateParams, eventService)
{
    var SHOW_DATE_FORMAT = 'YYYYMMDD';

    var ctrl = this;

    ctrl.calendarMonth = null;
    ctrl.eventsPage = null;

    ctrl._populateEvents = function(eventsPage)
    {
        ctrl.eventsPage = eventsPage;

        var events = eventsPage.events;

        var eventModels = [];

        for(var i = 0; i < events.length; i++)
        {
            eventModels.push(new EventModel(events[i]));
        }

        ctrl.calendarMonth.populateEvents(eventModels);
    };

    ctrl._init = function()
    {
        var currentRaidWeek = new Calendar.RaidWeekModel(new Date());

        var showDate = moment($stateParams.showDate, SHOW_DATE_FORMAT);

        if (!showDate.isValid()) showDate = new Date();

        ctrl.calendarMonth = new Calendar.MonthModel(showDate, currentRaidWeek);

        var promise = eventService.getEventsPage(ctrl.calendarMonth.firstDate);

        promise.then(
            function(eventsPage)
            {
                try
                {
                    ctrl.olderTimestamp = moment(eventsPage.previousTimestamp).format(SHOW_DATE_FORMAT);
                    ctrl.newerTimestamp = moment(eventsPage.nextTimestamp).format(SHOW_DATE_FORMAT);

                    ctrl._populateEvents(eventsPage);
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
    };

    ctrl._init();
});
