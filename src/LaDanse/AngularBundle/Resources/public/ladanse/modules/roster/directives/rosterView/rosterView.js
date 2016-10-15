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
.controller('RosterViewCtrl', function($scope, $rootScope, $http, Notification)
{
    var ctrl = this;

    ctrl.searchCriteria = null;
    ctrl.searchResult = null;

    ctrl.searchCallback = function(searchCriteria)
    {
        ctrl.searchCriteria = searchCriteria;

        var restUrl = Routing.generate('getCharactersByCriteria');

        $http.post(restUrl, searchCriteria)
            .success(function(searchResult)
            {
                ctrl.searchResult = searchResult;

                if (ctrl.searchResult.length == 0)
                {
                    Notification.warning('Your search did not return a result');
                }
            })
            .error(function()
            {
                Notification.error('Search failed');
            });
    }
});
