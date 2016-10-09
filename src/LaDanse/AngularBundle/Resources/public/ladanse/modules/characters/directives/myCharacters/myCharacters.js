/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var rosterModule = GetAngularModule(ROSTER_MODULE_NAME)

rosterModule.directive('myCharacters', function()
{
    return {
        restrict: 'E',
        controller: 'MyCharactersCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/characters/directives/myCharacters/myCharacters.html')
    };
})
.controller('MyCharactersCtrl', function($scope, $rootScope, gameDataService, $http)
{
    var ctrl = this;

    ctrl.initialized = false;
    ctrl.gameDataModel = null;
    ctrl.claimedCharactersData = null;

    ctrl.claimedCharacters = null;

    ctrl.initData = function()
    {
        var characterFactory = new Models.Characters.CharacterFactory();

        ctrl.claimedCharacters = characterFactory.createCharacterArray(ctrl.gameDataModel, ctrl.claimedCharactersData);
    }

    ctrl.verifyData = function()
    {
        if (!(ctrl.gameDataModel === null || ctrl.claimedCharactersData === null))
        {
            ctrl.initData();

            ctrl.initialized = true;
        }
    }

    ctrl.fetchData = function()
    {
        var restUrl = Routing.generate('getCharactersClaimedByAccount', {'accountId': 1});

        gameDataService.getGameData()
            .then(function(gameDataModel)
            {
                ctrl.gameDataModel = gameDataModel;
                ctrl.verifyData();
            });

        $http.get(restUrl)
            .success(function(claimedCharacters)
            {
                ctrl.claimedCharactersData = claimedCharacters;
                ctrl.verifyData();
            });
    };

    ctrl.fetchData();
});
