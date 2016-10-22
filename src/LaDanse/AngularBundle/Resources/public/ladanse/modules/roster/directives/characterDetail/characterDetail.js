/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var rosterModule = GetAngularModule(ROSTER_MODULE_NAME)

rosterModule.directive('characterDetail', function()
{
    return {
        restrict: 'E',
        controller: 'CharacterDetailCtrl',
        controllerAs: 'ctrl',
        scope: {
            'character': '=',
            'callback': '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/directives/characterDetail/characterDetail.html')
    };
})
.controller('CharacterDetailCtrl', function($scope, $rootScope, $window)
{
    var ctrl = this;

    ctrl.currentAccount = $window.currentAccount;

    ctrl.character = $scope.character;
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

    ctrl.closeClicked = function()
    {
        ctrl.callback.close();
    };

    ctrl.claimClicked = function()
    {
        ctrl.callback.claim(ctrl.character.id);
    };

    ctrl.initForm();
});
