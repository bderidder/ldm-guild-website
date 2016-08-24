forumControllers.controller('TopicPageCtrl',
    function($scope, $routeParams, $rootScope, $http, $anchorScroll, forumService)
    {
        $scope.forumId = $routeParams.forumId;
        $scope.topicId = $routeParams.topicId;

        $scope.createorId = 0;
        $scope.creator = '';
        $scope.subject = '';
        $scope.createDate = '';
        $scope.showRead = true;

        $scope.posts = [];

        $scope.isTopicLoaded = false;
        $scope.hasUnreadItems = false;

        $scope.initTopicPageCtrl = function()
        {
            $scope.refreshPosts();

            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    $scope.hasUnreadItems = lastChangesModel.hasTopicChanged($scope.topicId);
                });
        };

        $scope.toggleShowRead = function()
        {
            $scope.showRead = !$scope.showRead;
        }

        $scope.markAllAsReadClicked = function()
        {
            console.log('marking all as read in topic');

            forumService.markTopicAsRead($scope.topicId);

            $scope.showRead = true;

            $scope.refreshPosts();
            $scope.updateTopicUnreadPosts();
        }

        $scope.updateTopicUnreadPosts = function()
        {
            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    $scope.hasUnreadItems = lastChangesModel.hasTopicChanged($scope.topicId);
                });
        }

        $scope.scrollToFirstUnread = function ()
        {
            if ($scope.hasUnreadItems)
            {
                forumService.getChangesForUser()
                    .then(function(lastChangesModel)
                    {
                        for (var i = 0; i < $scope.posts.length; i++)
                        {
                            var post = $scope.posts[i];

                            if (lastChangesModel.isPostNew(post.postId))
                            {
                                console.log('Jumping to Post_' + post.postId);

                                $anchorScroll("Post_" + post.postId);

                                return;
                            }
                        }
                    });
            }
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