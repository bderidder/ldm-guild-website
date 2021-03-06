var commentsModule = GetAngularModule(COMMENTS_MODULE_NAME);

commentsModule.controller('CommentGroupCtrl', function ($scope, $rootScope, $http) {

    $scope.comments = [];
    $scope.groupId = 0;

    $scope.initCommentGroupCtrl = function()
    {
        $scope.groupId = commentGroupId;

        $scope.refreshPosts();
    };

    $scope.refreshPosts = function()
    {
        $http.get('/services/comment/groups/' + $scope.groupId)
            .then(
                function(httpRestResponse)
                {
                    $scope.comments = httpRestResponse.data.comments;
                }
            );
    };

    $scope.currentEdited = {};
    $scope.currentEdited.controller = null

});