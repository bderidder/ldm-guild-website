/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var charactersModule = GetAngularModule(CHARACTERS_MODULE_NAME);

charactersModule.directive('myCharacters', function()
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

    ctrl.editedCharacter = null;

    ctrl.editCharacter = function(characterId)
    {
        if (!ctrl.editedCharacter)
        {
            ctrl.editedCharacter = characterId;
        }
        else
        {
            ctrl.cancelEditMode();
        }
    };

    ctrl.cancelEditMode = function()
    {
        ctrl.editedCharacter = null;
    };

    ctrl.initData = function()
    {
        var characterFactory = new Models.Characters.CharacterFactory();

        ctrl.claimedCharacters = characterFactory.createCharacterArray(ctrl.gameDataModel, ctrl.claimedCharactersData);
    };

    ctrl.verifyData = function()
    {
        if (!(ctrl.gameDataModel === null || ctrl.claimedCharactersData === null))
        {
            ctrl.initData();

            ctrl.initialized = true;
        }
    };

    ctrl.fetchData = function()
    {
        var restUrl = Routing.generate('getCharactersClaimedByAccount', {'accountId': currentAccount.id});

        gameDataService.getGameData()
            .then(
                function(gameDataModel)
                {
                    ctrl.gameDataModel = gameDataModel;
                    ctrl.verifyData();
                }
            );

        $http.get(restUrl)
            .then(
                function(httpRestResponse)
                {
                    ctrl.claimedCharactersData = httpRestResponse.data;
                    ctrl.verifyData();
                }
            );
    };

    ctrl.updateClaim = function(updateModel)
    {
        var jsonData = {};
        jsonData.comment = updateModel.comment;
        jsonData.raider = updateModel.raider;

        var roles = [];
        updateModel.playsTank ? roles.push('Tank') : null;
        updateModel.playsHealer ? roles.push('Healer') : null;
        updateModel.playsDPS ? roles.push('DPS') : null;

        jsonData.roles = roles;

        var restUrl = Routing.generate('putClaim', {'characterId': ctrl.editedCharacter});

        $http.put(restUrl, jsonData)
            .then(
                function()
                {
                    alertify.success('Claim updated');
                    ctrl.fetchData();
                    ctrl.cancelEditMode();
                },
                function()
                {
                    alertify.error("Could not update claim");
                    ctrl.cancelEditMode();
                }
            );
    };

    ctrl.removeClaim = function()
    {
        var restUrl = Routing.generate('deleteClaim', {'characterId': ctrl.editedCharacter});

        $http.delete(restUrl)
            .then(
                function()
                {
                    alertify.success("Claim removed");
                    ctrl.fetchData();
                    ctrl.cancelEditMode();
                },
                function()
                {
                    alertify.error("Could not remove claim");
                    ctrl.cancelEditMode();
                }
            );
    };

    ctrl.editCallback = {};
    ctrl.editCallback.update = function(updateModel)
    {
        ctrl.updateClaim(updateModel);
    };
    ctrl.editCallback.cancel = function()
    {
        ctrl.cancelEditMode();
    };
    ctrl.editCallback.remove = function()
    {
        alertify.confirm(
            'Confirm Remove Claim',
            'This will remove your claim on this character, are you sure?',
            function()
            {
                ctrl.removeClaim();
            },
            function() {} // do nothing on cancel
        );
    };

    ctrl.fetchData();
});
