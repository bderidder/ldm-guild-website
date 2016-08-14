'use strict';

angular.module('LaDanseApp.forums', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/forums', {
    templateUrl: '../bundles/ladanseangular/ladanse/pages/forums/forums.html',
    controller: 'ForumsCtrl'
  });
}])

.controller('ForumsCtrl', [function() {

}]);