'use strict';

angular.module('LaDanseApp.about', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/about', {
    templateUrl: '../bundles/ladanseangular/ladanse/pages/about/about.html',
    controller: 'AboutCtrl'
  });
}])

.controller('AboutCtrl', [function() {

}]);