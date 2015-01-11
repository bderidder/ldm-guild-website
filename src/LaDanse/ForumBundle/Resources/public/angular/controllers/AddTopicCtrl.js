forumControllers.controller('AddTopicCtrl', function ($scope, $rootScope, $http) {

    $scope.newSubject = {};
    $scope.newText = {};
    $scope.subjectMaxLength = 58;
    $scope.textMaxLength = 32768;
    $scope.collapsed = true;

    $scope.initAddTopicCtrl = function()
    {
        $scope.newSubject.value = "";
        $scope.newText.value = "";
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
        var subjectValue = $scope.newSubject.value;
        var textValue = $scope.newText.value;

        if ((subjectValue.trim().length < 5) || (subjectValue.trim().length > $scope.subjectMaxLength) )
        {
            return;
        }

        if ((textValue.trim().length < 5) || (textValue.trim().length > $scope.textMaxLength) )
        {
            return;
        }

        $http.post('../services/forum/forums/' + $scope.forumId + "/topics",
            {
                subject: subjectValue.trim(),
                text: textValue.trim()
            }).
            success(function(data, status, headers, config)
            {
                $scope.newSubject.value = "";
                $scope.newText.value = "";
                $scope.hideAndReset();
                $scope.refreshTopics();
            }).
            error(function(data, status, headers, config)
            {
            });
    }
});