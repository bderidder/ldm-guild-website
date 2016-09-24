var commentsModule = GetAngularModule(COMMENTS_MODULE_NAME);

commentsModule.controller('CommentCtrl', function ($scope, $rootScope, $http) {

    $scope.editing = false;
    $scope.editedValue = "";

    $scope.initCommentCtrl = function(comment)
    {
        $scope.comment = comment;
    };

    $scope.editClicked = function()
    {
        $scope.editedValue = $scope.comment.message;

        if ($scope.currentEdited.controller !== null)
        {
            $scope.currentEdited.controller.stopEditing();
        }

        $scope.editing = true;

        $scope.currentEdited.controller = $scope;

        $rootScope.$broadcast('CommentsApp.EditComment.Started', $scope.comment.postId);
    }

    $scope.isEditable = function()
    {
        return $scope.comment.posterId == currentAccount.id;
    }

    $scope.stopEditing = function()
    {
        $scope.editing = false;

        $rootScope.$broadcast('CommentsApp.EditComment.Stopped', $scope.comment.postId);
    }

    $scope.cancelCommentEditor = function()
    {
        $scope.editing = false;
        $scope.currentEdited.controller = null;

        $rootScope.$broadcast('CommentsApp.EditComment.Cancelled', $scope.comment.postId);
    }

    $scope.saveCommentEditor = function(newValue)
    {
        $scope.editing = false;
        $scope.currentEdited.controller = null;

        $http.post('/services/comment/comments/' + $scope.comment.postId,
            {
                message: newValue.trim()
            }).
            success(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.EditComment.Succeeded', $scope.comment.postId);
                $scope.comment.message = newValue;
                $scope.editedValue = "";
            }).
            error(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.EditComment.Failed', $scope.comment.postId);
            });
    }

});