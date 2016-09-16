/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumSubApp.controller('PostEditorCtrl', function ($scope, $rootScope) {

    $scope.maxLength = 32768;

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