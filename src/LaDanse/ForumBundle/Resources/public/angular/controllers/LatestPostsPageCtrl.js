forumControllers.controller('LatestPostsPageCtrl',
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