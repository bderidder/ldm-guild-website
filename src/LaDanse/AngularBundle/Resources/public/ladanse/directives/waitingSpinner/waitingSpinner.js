/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumsModule.directive('waitingSpinner', function()
{
    return {
        restrict: 'E',
        controller: 'WaitingSpinnerCtrl',
        controllerAs: 'ctrl',
        scope: {
            show: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/directives/waitingSpinner/waitingSpinner.html')
    };
})
.controller('WaitingSpinnerCtrl', function($scope, $rootScope)
{
});
