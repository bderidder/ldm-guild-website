angular.module('LaDanseApp')

    .directive('calendarNav', function()
    {
        return {
            restrict: 'E',
            replace: true,
            controller: 'CalendarNavCtrl',
            controllerAs: 'calendarNav',
            scope: true,
            templateUrl: '../bundles/ladanseangular/new/directives/calendarNav/calendarNav.html'
        };
    })

    .controller('CalendarNavCtrl', function()
    {
    });
