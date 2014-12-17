forumControllers.controller('PostCtrl',
    function($scope, $routeParams, $rootScope, $http)
    {
        $scope.isEditing = false;

        $scope.initPostCtrl = function(post)
        {
            $scope.post = post;
        };

        $scope.editRequested = function()
        {
            $scope.isEditing = true;
        }

        $scope.cancelPostEditor = function()
        {
            $scope.isEditing = false;
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