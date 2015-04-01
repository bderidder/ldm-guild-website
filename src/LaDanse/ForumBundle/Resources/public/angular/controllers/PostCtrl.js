forumControllers.controller('PostCtrl',
    function($scope, $routeParams, $rootScope, $http, $timeout, forumService)
    {
        $scope.isEditing = false;
        $scope.isNew = false;
        $scope.markReadTimer = null;

        $scope.initPostCtrl = function(post)
        {
            $scope.post = post;

            $scope.initIsNew();

            $scope.$on('$destroy', function ()
            {
                if ($scope.markReadTimer != null)
                {
                    $timeout.cancel($scope.markReadTimer);
                    $scope.markReadTimer = null;
                }
            });
        };

        $scope.canUserEdit = function()
        {
            return ($scope.post.posterId == currentAccount.id);
        }

        $scope.editRequested = function()
        {
            $scope.isEditing = true;
        }

        $scope.cancelPostEditor = function()
        {
            $scope.isEditing = false;
        }

        $scope.inViewChanged = function(inView)
        {
            if (inView && $scope.isNew)
            {
                // post is visible and is new
                $scope.markReadTimer = $timeout(function()
                {
                    $scope.markAsReadClicked();
                    $scope.markReadTimer = null;
                }, 10000);
            }
            else if (!inView && ($scope.markReadTimer != null))
            {
                // post is not visible anymore and we have a timer
                $timeout.cancel($scope.markReadTimer);
                $scope.markReadTimer = null;
            }

            console.log("inViewChanged called for " + $scope.post.postId + " - " + inView)
        }

        $scope.markAsReadClicked = function()
        {
            forumService.markPostAsRead($scope.post.postId);

            $scope.updateTopicUnreadPosts();
            $scope.initIsNew();
        }

        $scope.initIsNew = function()
        {
            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    $scope.isNew = lastChangesModel.isPostNew($scope.post.postId);
                });
        }

        $scope.savePostEditor = function(postValue)
        {
            $scope.post.message = postValue;

            $http.post('../services/forum/posts/' + $scope.post.postId,
                {
                    message: postValue
                }).
                success(function(data, status, headers, config)
                {
                    $scope.isEditing = false;
                    $scope.refreshPosts();
                }).
                error(function(data, status, headers, config)
                {
                    // posting failed
                });
        }
    }
);