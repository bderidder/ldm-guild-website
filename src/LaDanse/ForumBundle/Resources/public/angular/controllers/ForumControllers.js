var MAX_COMMENT_LENGTH = 250;

forumControllers.controller('ForumGroupCtrl', function ($scope, $rootScope, $http) {

    $scope.comments = [];
    $scope.groupId = 0;

    $scope.initCommentGroupCtrl = function()
    {
        $scope.groupId = forumId;

        $scope.refreshPosts();
    };

    $scope.refreshPosts = function()
    {
        $http.get('../services/forum/forums/' + forumId).success(function(data) {
            $scope.comments = data.posts;
        });
    }

    $scope.currentEdited = {};
    $scope.currentEdited.controller = null

});

forumControllers.controller('CommentCtrl', function ($scope, $rootScope, $http) {

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

    $scope.escapePressed = function()
    {
        $scope.editing = false;
        $scope.currentEdited.controller = null;

        $rootScope.$broadcast('CommentsApp.EditComment.Cancelled', $scope.comment.postId);
    }

    $scope.enterPressed = function(newValue)
    {
        if ((newValue.trim().length < 5) || (newValue.trim().length > MAX_COMMENT_LENGTH) )
        {
            return;
        }

        $scope.editing = false;
        $scope.currentEdited.controller = null;

        $http.post('../services/comment/comments/' + $scope.comment.postId,
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

forumControllers.controller('AddCommentCtrl', function ($scope, $rootScope, $http) {

    $scope.message = "";
    $scope.maxLength = 250;

    $scope.initAddCommentCtrl = function()
    {
    };

    $scope.showEditor = function()
    {
        $scope.visible = true;
    }

    $scope.hideEditor = function(f)
    {
        $scope.visible = false;
    }

    $scope.updateCharacterUsage = function()
    {
        $scope.characterUsage = $scope.message.length + "/" + MAX_COMMENT_LENGTH;
    }

    $scope.escapePressed = function()
    {
        $rootScope.$broadcast('CommentsApp.AddComment.Cancelled');

        $scope.message = "";
    }

    $scope.enterPressed = function(newValue)
    {
        if ((newValue.trim().length < 5) || (newValue.trim().length > MAX_COMMENT_LENGTH) )
        {
            return;
        }

        $http.post('../services/forum/forums/' + forumId + "/topics",
            {
                subject: newValue.trim()
            }).
            success(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.AddComment.Succeeded');
                $scope.message = "";
                $scope.refreshPosts();
            }).
            error(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.AddComment.Failed');
            });
    }

});