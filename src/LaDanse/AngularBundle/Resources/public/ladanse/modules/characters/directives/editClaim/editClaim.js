/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var charactersModule = GetAngularModule(CHARACTERS_MODULE_NAME);

charactersModule.directive('editClaim', function()
{
    return {
        restrict: 'E',
        controller: 'EditClaimCtrl',
        controllerAs: 'ctrl',
        scope: {
            'character': '&',
            'callback': '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/characters/directives/editClaim/editClaim.html')
    };
})
.controller('EditClaimCtrl', function($scope, $rootScope)
{
    var ctrl = this;

    ctrl.character = $scope.character();
    ctrl.callback = $scope.callback;

    ctrl.initForm = function()
    {
        ctrl.form = {};

        if (ctrl.character.claim)
        {
            ctrl.form.playsTank = ctrl.character.claim.hasRole('Tank');
            ctrl.form.playsHealer = ctrl.character.claim.hasRole('Healer');
            ctrl.form.playsDPS = ctrl.character.claim.hasRole('DPS');
            ctrl.form.comment = ctrl.character.claim.getComment();
            ctrl.form.raider = ctrl.character.claim.getRaider();

            if (ctrl.character.getLevel() != 110)
            {
                ctrl.form.raider = false;
            }
        }
        else
        {
            ctrl.form.playsTank = false;
            ctrl.form.playsHealer = false;
            ctrl.form.playsDPS = false;
            ctrl.form.comment = "";
            ctrl.form.raider = false;
        }
    };

    ctrl.updateClicked = function()
    {
        ctrl.callback.update(ctrl.form);
    };

    ctrl.cancelClicked = function()
    {
        ctrl.callback.cancel();
    };

    ctrl.removeClicked = function()
    {
        ctrl.callback.remove();
    };

    ctrl.initForm();
});
