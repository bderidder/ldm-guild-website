angular.module('LaDanseApp')

    .directive('calendarNav', function()
    {
        return {
            restrict: 'E',
            replace: true,
            controller: 'CalendarNavCtrl',
            controllerAs: 'calendarNav',
            scope: true,
            templateUrl: '../bundles/ladanseangular/ladanse/directives/calendarNav/calendarNav.html'
        };
    })

    .controller('CalendarNavCtrl', function()
    {
    });
