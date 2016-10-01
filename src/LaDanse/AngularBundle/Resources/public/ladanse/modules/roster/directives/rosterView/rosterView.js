/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var rosterModule = GetAngularModule(ROSTER_MODULE_NAME)

rosterModule.directive('rosterView', function()
{
    return {
        restrict: 'E',
        controller: 'RosterViewCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/directives/rosterView/rosterView.html')
    };
})
.controller('RosterViewCtrl', function($scope, $rootScope)
{
    var ctrl = this;

    ctrl.searchCriteria = null;

    ctrl.searchCallback = function(searchCriteria)
    {
        console.log('RosterViewCtrl - someFunctionName - ' + searchCriteria);

        ctrl.searchCriteria = searchCriteria;
    }
});
