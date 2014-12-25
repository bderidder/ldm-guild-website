forumControllers.controller('ForumItemCtrl',
    function($scope, $routeParams, $rootScope, $http, forumService)
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
            $http.get('../services/forum/forums/' + $scope.forumId + "/activity").success(function(data) {
                $scope.recentPosts = data.posts;
            });
        }

        $scope.initRecentActivity = function()
        {
            forumService.getLastActivity()
                .then(function(activityModel)
                {
                    $scope.isRecentlyUpdated = activityModel.isForumInActivity($scope.forumId);
                });
        }
    }
);