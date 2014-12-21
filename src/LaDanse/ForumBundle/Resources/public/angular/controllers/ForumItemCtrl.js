forumControllers.controller('ForumItemCtrl',
    function($scope, $routeParams, $rootScope, $http)
    {
        $scope.initForumItemCtrl = function(forumId)
        {
            $scope.forumId = forumId;

            var forum = $scope.getForum($scope.forumId);

            $scope.name = forum.name;
            $scope.description = forum.description;
            $scope.topics = forum.topics;
            $scope.isForumLoaded = true;

            $scope.refreshActivity();
        };

        $scope.refreshActivity = function()
        {
            $http.get('../services/forum/forums/' + $scope.forumId + "/activity").success(function(data) {
                $scope.recentPosts = data.posts;
            });
        }

    }
);