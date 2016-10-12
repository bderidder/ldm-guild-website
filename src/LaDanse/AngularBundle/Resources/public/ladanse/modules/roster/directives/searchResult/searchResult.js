/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var rosterModule = GetAngularModule(ROSTER_MODULE_NAME)

rosterModule.directive('searchResult', function()
{
    return {
        restrict: 'E',
        controller: 'SearchResultCtrl',
        controllerAs: 'ctrl',
        scope: {
            searchResult: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/directives/searchResult/searchResult.html')
    };
})
.controller('SearchResultCtrl', function($scope, $rootScope, gameDataService)
{
    var ctrl = this;

    ctrl.searchResult = $scope.searchResult;
    ctrl.characters = null;

    $scope.$watch('searchResult', function()
    {
        ctrl.searchResult = $scope.searchResult;
        ctrl.fetchData();
    });

    ctrl.fetchData = function()
    {
        console.log("fetchData");

        gameDataService.getGameData()
            .then(function(gameDataModel)
            {
                ctrl.gameDataModel = gameDataModel;

                var characterFactory = new Models.Characters.CharacterFactory();

                ctrl.characters = characterFactory.createCharacterArray(ctrl.gameDataModel, ctrl.searchResult);
            });
    };

    //ctrl.fetchData();
});
