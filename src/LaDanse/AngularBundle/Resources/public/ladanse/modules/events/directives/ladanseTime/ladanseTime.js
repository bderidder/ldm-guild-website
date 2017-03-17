/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('ladanseTime', function()
{
    return {
        restrict: 'E',
        controller: 'LaDanseCtrl',
        controllerAs: 'ctrl',
        scope: {
            time: '=',
            format: '=',
            showServerTime: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/ladanseTime/ladanseTime.html')
    };
})
.controller('LaDanseCtrl', function($scope, $rootScope)
{
    var ctrl = this;

    ctrl.format = $scope.format;

    $scope.$watch(
        'showServerTime',
        function ()
        {
            ctrl.updateTimeZone();
        }
    );

    ctrl.toggleTimeZoneShown = function()
    {
        $scope.showServerTime = !$scope.showServerTime;

        ctrl.updateTimeZone();
    };

    ctrl.updateTimeZone = function()
    {
        if ($scope.showServerTime)
        {
            ctrl.time = moment($scope.time);
        }
        else
        {
            ctrl.time = moment($scope.time).tz("Africa/Johannesburg");
        }
    };

    ctrl.updateTimeZone();
});
