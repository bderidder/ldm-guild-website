forumControllers.controller('AddTopicCtrl', function ($scope, $rootScope, $http) {

    $scope.subject = "";
    $scope.maxLength = 100;
    $scope.collapsed = true;

    $scope.initAddTopicCtrl = function()
    {
    };

    $scope.showButtonClicked = function()
    {
        $scope.collapsed = false;
    }

    $scope.cancelButtonClicked = function()
    {
        $scope.hideAndReset();
    }

    $scope.hideAndReset = function()
    {
        $scope.collapsed = true;
    }

    $scope.addButtonClicked = function()
    {
        if (($scope.subject.trim().length < 5) || ($scope.subject.trim().length > $scope.maxLength) )
        {
            return;
        }

        $http.post('../services/forum/forums/' + $scope.forumId + "/topics",
            {
                subject: $scope.subject.trim()
            }).
            success(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.AddComment.Succeeded');
                $scope.subject = "";
                $scope.hideAndReset();
                $scope.refreshTopics();
            }).
            error(function(data, status, headers, config)
            {
                $rootScope.$broadcast('CommentsApp.AddComment.Failed');
            });
    }
});