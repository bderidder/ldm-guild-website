forumControllers.controller('AddTopicCtrl', function ($scope, $rootScope, $http) {

    $scope.newSubject = {};
    $scope.maxLength = 55;
    $scope.collapsed = true;

    $scope.initAddTopicCtrl = function()
    {
        $scope.newSubject.value = "";
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

    $scope.addButtonClicked = function(value)
    {
        var subjectValue = $scope.newSubject.value;

        if ((subjectValue.trim().length < 5) || (subjectValue.trim().length > $scope.maxLength) )
        {
            return;
        }

        $http.post('../services/forum/forums/' + $scope.forumId + "/topics",
            {
                subject: subjectValue.trim()
            }).
            success(function(data, status, headers, config)
            {
                $scope.subject = "";
                $scope.hideAndReset();
                $scope.refreshTopics();
            }).
            error(function(data, status, headers, config)
            {
            });
    }
});