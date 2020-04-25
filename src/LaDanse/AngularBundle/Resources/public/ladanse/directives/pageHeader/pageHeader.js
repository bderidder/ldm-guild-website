/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumsModule.directive('pageHeader', function()
{
    return {
        restrict: 'E',
        replace: true,
        controller: 'PageHeaderCtrl',
        controllerAs: 'ctrl',
        scope: {
            title: '@',
            url: '@'
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/directives/pageHeader/pageHeader.html')
    };
})
.controller('PageHeaderCtrl', function($scope, $rootScope, $state)
{
    $scope.hasUrl = function()
    {
        return !(angular.isUndefined($scope.url) || $scope.url === null || $scope.url.length == 0);
    }

    $scope.backClicked = function($event)
    {
        console.log("Back clicked!");

        $event.stopPropagation();
        $event.preventDefault();

        if (currentAccount.id == 5)
        {
            if (document.referrer == "" || window.history.length < 2)
            {
                window.location.assign('/menu/');
            }
            else
            {
                window.history.back();
            }
        }
        else
        {
            window.location.assign($scope.url);
        }
    }
});
