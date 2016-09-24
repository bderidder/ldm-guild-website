/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('HeaderCtrl',
    function($scope, $rootScope, $http)
    {
        $scope.initHeaderCtrl = function(headerText, headerURL)
        {
            $scope.headerText = headerText;
            $scope.headerURL = headerURL;
        };

        $scope.hasUrl = function()
        {
            return !(angular.isUndefined($scope.headerURL) || $scope.headerURL === null);
        }
    }
);