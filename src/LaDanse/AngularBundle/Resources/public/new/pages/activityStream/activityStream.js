'use strict';

angular.module('LaDanseApp.activityStream', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/activityStream', {
    templateUrl: '../bundles/ladanseangular/new/pages/activityStream/activityStream.html',
    controller: 'ActivityStreamCtrl'
  });
}])

.controller('ActivityStreamCtrl', [function() {

}]);