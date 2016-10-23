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
.controller('RosterViewCtrl', function($scope, $rootScope, $stateParams, $http, Notification, logCallbackService)
{
    var ctrl = this;

    ctrl.searchCriteria = null;
    ctrl.searchResult = null;

    ctrl.init = function()
    {
        var criteriaQuery = $stateParams.criteria;

        if (criteriaQuery != undefined)
        {
            try
            {
                var jsonCriteria = atob(criteriaQuery);

                ctrl.searchCriteria = new SearchCriteria(JSON.parse(jsonCriteria));

                ctrl.searchCallback(ctrl.searchCriteria);
            }
            catch(e)
            {
                ctrl.searchCriteria = new SearchCriteria();
            }
        }
        else
        {
            ctrl.searchCriteria = new SearchCriteria();
        }
    }

    ctrl.searchCallback = function(searchCriteria)
    {
        ctrl.searchCriteria = searchCriteria;

        var restUrl = Routing.generate('getCharactersByCriteria');

        $http.post(restUrl, searchCriteria)
            .success(function(searchResult)
            {
                searchResult.sort(function(a, b)
                {
                    return a.name.localeCompare(b.name);
                });

                ctrl.searchResult = searchResult;

                if (ctrl.searchResult.length == 0)
                {
                    Notification.warning('Your search did not return a result');
                }
            })
            .error(function()
            {
                logCallbackService.log('RosterViewCtrl', 'Could not execute search request');

                Notification.error('Search failed');
            });
    }

    ctrl.init();
});
