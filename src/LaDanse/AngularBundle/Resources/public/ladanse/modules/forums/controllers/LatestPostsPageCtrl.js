/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('LatestPostsPageCtrl',
    function($scope, forumService)
    {
        $scope.initLatestPostsPageCtrl = function()
        {
            $scope.initRecentActivity();
        }

        $scope.initRecentActivity = function()
        {
            forumService.getLastActivity()
                .then(function(activityModel)
                {
                    $scope.latest = activityModel;
                });
        }
    }
);