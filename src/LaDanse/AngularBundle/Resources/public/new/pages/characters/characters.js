'use strict';

angular.module('LaDanseApp.characters', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/characters', {
    templateUrl: '../bundles/ladanseangular/new/pages/characters/characters.html',
    controller: 'CharactersCtrl'
  });
}])

.controller('CharactersCtrl', [function() {

}]);