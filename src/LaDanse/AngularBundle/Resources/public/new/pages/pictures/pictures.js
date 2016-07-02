'use strict';

angular.module('LaDanseApp.pictures', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/pictures', {
    templateUrl: '../bundles/ladanseangular/new/pages/pictures/pictures.html',
    controller: 'PicturesCtrl'
  });
}])

.controller('PicturesCtrl', [function() {

}]);