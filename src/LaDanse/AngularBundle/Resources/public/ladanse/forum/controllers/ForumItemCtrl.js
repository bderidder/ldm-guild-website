/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('ForumItemCtrl',
    function($scope, $rootScope, $http, forumService)
    {
        $scope.initForumItemCtrl = function(forumId)
        {
            $scope.forumId = forumId;

            var forum = $scope.getForum($scope.forumId);

            $scope.name = forum.name;
            $scope.description = forum.description;
            $scope.topics = forum.topics;
            $scope.isForumLoaded = true;
            $scope.isRecentlyUpdated = false;

            $scope.refreshActivity();
            $scope.initRecentActivity();
        };

        $scope.refreshActivity = function()
        {
            $http.get('/services/forum/forums/' + $scope.forumId + "/activity").success(function(data) {
                $scope.recentPosts = data.posts;
            });
        }

        $scope.initRecentActivity = function()
        {
            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    $scope.isRecentlyUpdated = lastChangesModel.hasForumChanged($scope.forumId);
                });
        }
    }
);