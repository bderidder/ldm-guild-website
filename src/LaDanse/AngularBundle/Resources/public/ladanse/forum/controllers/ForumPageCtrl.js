/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('ForumPageCtrl',
    function ($scope, $rootScope, $http)
    {
        $scope.forumId = $rootScope.$state.params.forumId;

        $scope.name = '';
        $scope.topics = [];

        $scope.isForumLoaded = false;

        $scope.initForumCtrl = function()
        {
            $scope.refreshTopics();
        };

        $scope.refreshTopics = function()
        {
            $http.get('/services/forum/forums/' + $scope.forumId).success(function(data) {
                $scope.name = data.name;
                $scope.topics = data.topics;
                $scope.isForumLoaded = true;
            });
        }
    }
);