forumControllers.controller('PostCtrl',
    function($scope, $routeParams, $rootScope, $http, forumService)
    {
        $scope.isEditing = false;
        $scope.isNew = false;

        $scope.initPostCtrl = function(post)
        {
            $scope.post = post;

            $scope.initIsNew();
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

        $scope.markAsReadClicked = function()
        {
            forumService.getChangesForUser()
                .then(function(lastChangesModel)
                {
                    lastChangesModel.markPostAsRead($scope.post.postId);
                });

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