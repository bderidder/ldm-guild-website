'use strict';

// Declare app level module which depends on views, and components
angular.module('LaDanseApp', [
    'ngRoute',
    'angularGrid',
    'jQueryScrollbar',
    'LaDanseApp.about',
    'LaDanseApp.activityStream',
    'LaDanseApp.calendar',
    'LaDanseApp.characters',
    'LaDanseApp.forums',
    'LaDanseApp.pictures',
    'LaDanseApp.teamspeak'
])

.config(['$routeProvider', function($routeProvider)
{
  $routeProvider.otherwise({redirectTo: '/activityStream'});
}]);
