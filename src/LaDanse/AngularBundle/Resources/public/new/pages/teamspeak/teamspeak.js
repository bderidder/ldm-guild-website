'use strict';

angular.module('LaDanseApp.teamspeak', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/teamspeak', {
    templateUrl: '../bundles/ladanseangular/new/pages/teamspeak/teamspeak.html',
    controller: 'TeamspeakCtrl'
  });
}])

.controller('TeamspeakCtrl', [function() {

}]);