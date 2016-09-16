'use strict';

angular.module('LaDanseApp.calendar', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/calendar', {
    templateUrl: '../bundles/ladanseangular/ladanse/pages/calendar/calendar.html',
    controller: 'CalendarCtrl'
  });
}])

.controller('CalendarCtrl', [function() {

}]);