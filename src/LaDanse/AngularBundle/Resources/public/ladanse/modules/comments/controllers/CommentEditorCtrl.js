var commentsModule = GetAngularModule(COMMENTS_MODULE_NAME);

commentsModule.controller('CommentEditorCtrl', function ($scope, $rootScope, $http) {

    $scope.message = "";
    $scope.characterUsage = "0/0";
    $scope.maxLength = 250;

    $scope.initCommentEditorCtrl = function(message, enterCallback, cancelCallback)
    {
        $scope.message = message;
        $scope.enterCallback = enterCallback;
        $scope.cancelCallback = cancelCallback;
        $scope.$watch('message', $scope.updateCharacterUsage);
    };

    $scope.isTooMuchText = function()
    {
        return $scope.message.length > $scope.maxLength;
    };

    $scope.updateCharacterUsage = function()
    {
        $scope.characterUsage = $scope.message.length + "/" + $scope.maxLength;
    };

    $scope.escapePressed = function()
    {
        $scope.message = "";
        $scope.cancelCallback();
    };

    $scope.enterPressed = function()
    {
        console.log("Enter Pressed in Comment Editor");

        if (($scope.message.trim().length < 5) || ($scope.message.trim().length > $scope.maxLength) )
        {
            return;
        }

        $scope.enterCallback($scope.message);
        $scope.message = "";
    }

});