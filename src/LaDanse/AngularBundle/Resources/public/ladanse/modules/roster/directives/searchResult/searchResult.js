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
.controller('SearchResultCtrl', function($scope, $rootScope, $stateParams, $state, gameDataService, $http, Notification)
{
    var ctrl = this;

    ctrl.searchResult = $scope.searchResult;
    ctrl.characters = null;
    ctrl.detailCharacter = null;

    $scope.$watch('searchResult', function()
    {
        ctrl.searchResult = $scope.searchResult;
        ctrl.detailCharacter = null;
        ctrl.fetchData();
    });

    ctrl.rowClicked = function(characterId)
    {
        if (ctrl.detailCharacter === null)
        {
            ctrl.detailCharacter = characterId;
        }
        else
        {
            ctrl.detailCharacter = null;
        }
    };

    ctrl.fetchData = function()
    {
        gameDataService.getGameData()
            .then(function(gameDataModel)
            {
                ctrl.gameDataModel = gameDataModel;

                var characterFactory = new Models.Characters.CharacterFactory();

                ctrl.characters = characterFactory.createCharacterArray(ctrl.gameDataModel, ctrl.searchResult);
            });
    };

    ctrl.claimCharacter = function(characterId)
    {
        var restUrl = Routing.generate('postClaim', {'characterId': characterId});

        var newClaim =
        {
            "roles": [],
            "raider": false,
            "comment": null
        };

        $http.post(restUrl, newClaim)
            .success(function()
            {
                Notification.success("Character successfully claimed");
                $state.go('characters.home', $stateParams);
            })
            .error(function()
            {
                Notification.error("Failed to claim this character");
            });
    }

    ctrl.detailCallback = {};
    ctrl.detailCallback.claim = function(characterId)
    {
        ctrl.claimCharacter(characterId);
    };
    ctrl.detailCallback.close = function()
    {
        ctrl.detailCharacter = null;
    };
});
