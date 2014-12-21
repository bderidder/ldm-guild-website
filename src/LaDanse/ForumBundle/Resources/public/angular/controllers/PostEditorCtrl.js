forumControllers.controller('PostEditorCtrl', function ($scope, $rootScope) {

    $scope.maxLength = 2048;

    $scope.initPostEditorCtrl = function(text, saveLabel, enterCallback, cancelCallback)
    {
        $scope.text = text;
        $scope.saveLabel = saveLabel;
        $scope.enterCallback = enterCallback;
        $scope.cancelCallback = cancelCallback;
    };

    $scope.cancelButtonClicked = function()
    {
        $scope.cancelCallback();
        $scope.text = "";
    }

    $scope.saveButtonClicked = function()
    {
        if (($scope.text.trim().length < 5) || ($scope.text.trim().length > $scope.maxLength) )
        {
            return;
        }

        $scope.enterCallback($scope.text.trim());
        $scope.text = "";
    }

});