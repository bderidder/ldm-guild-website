commentControllers.controller('AddCommentCtrl', function ($scope, $rootScope, $http) {

    $scope.message = "";
    $scope.visible = true;

    $scope.initAddCommentCtrl = function()
    {
        var unbindStarted =
            $rootScope.$on('CommentsApp.EditComment.Started', $scope.showEditor);
        var unbindCancelled =
            $rootScope.$on('CommentsApp.EditComment.Cancelled', $scope.showEditor);
        var unbindSucceeded =
            $rootScope.$on('CommentsApp.EditComment.Succeeded', $scope.showEditor);
        var unbindFailed =
            $rootScope.$on('CommentsApp.EditComment.Failed', $scope.showEditor);

        $scope.$on('$destroy', unbindStarted);
        $scope.$on('$destroy', unbindCancelled);
        $scope.$on('$destroy', unbindSucceeded);
        $scope.$on('$destroy', unbindFailed);
    };

    $scope.showEditor = function()
    {
        $scope.visible = true;
    }

    $scope.hideEditor = function(f)
    {
        $scope.visible = false;
    }

    $scope.cancelCommentEditor = function()
    {
        $rootScope.$broadcast('CommentsApp.AddComment.Cancelled');

        $scope.message = "";
    }

    $scope.saveCommentEditor = function(newValue)
    {
        $http.post('../services/comment/groups/' + $scope.groupId + "/comments",
            {
                message: newValue.trim()
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