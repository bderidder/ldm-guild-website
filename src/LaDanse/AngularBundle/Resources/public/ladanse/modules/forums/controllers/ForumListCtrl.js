/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('ForumListCtrl',
    function ($scope, $rootScope, $http, $anchorScroll, forumService)
    {
        $scope.initForumListCtrl = function()
        {
            $scope.refreshTopics();
        };

        $scope.scrollTo = function (anchorId)
        {
            $anchorScroll(anchorId);
        };

        $scope.getForum = function(forumId)
        {
            var arrayLength = $scope.forums.length;
            for (var i = 0; i < arrayLength; i++)
            {
                var forum = $scope.forums[i];

                if (forum.forumId == forumId)
                {
                    return forum;
                }
            }

            return null;
        }

        $scope.refreshTopics = function()
        {
            $http.get('/services/forum/forums').success(function(data) {
                $scope.forums = data.forums;
                $scope.isForumLoaded = true;
            });
        }
    }
);