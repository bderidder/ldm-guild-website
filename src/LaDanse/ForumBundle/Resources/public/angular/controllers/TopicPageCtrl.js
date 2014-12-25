forumControllers.controller('TopicPageCtrl',
    function($scope, $routeParams, $rootScope, $http, forumService)
    {
        $scope.forumId = $routeParams.forumId;
        $scope.topicId = $routeParams.topicId;

        $scope.createorId = 0;
        $scope.creator = '';
        $scope.subject = '';
        $scope.createDate = '';

        $scope.posts = [];

        $scope.isTopicLoaded = false;

        $scope.initTopicPageCtrl = function()
        {
            $scope.refreshPosts();

            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    lastChangesModel.markTopicAsRead($scope.topicId);
                });
        };

        $scope.refreshPosts = function()
        {
            $http.get('../services/forum/topics/' + $scope.topicId).success(function(data) {
                $scope.topicId = data.topicId;
                $scope.createorId = data.creatorId;
                $scope.creator = data.creator;
                $scope.subject = data.subject;
                $scope.createDate = data.createDate;
                $scope.posts = data.posts;
                $scope.isTopicLoaded = true;
            });
        }

    }
);