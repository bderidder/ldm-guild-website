/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumSubApp.controller('AddPostCtrl', function ($scope, $rootScope, $http) {

    $scope.maxLength = 32768;
    $scope.collapsed = true;

    $scope.initAddPostCtrl = function()
    {
    };

    $scope.showButtonClicked = function()
    {
        $scope.collapsed = false;
    }

    $scope.cancelPostEditor = function()
    {
        $scope.hideAndReset();
    }

    $scope.hideAndReset = function()
    {
        $scope.collapsed = true;
    }

    $scope.savePostEditor = function(postValue)
    {
        $http.post('/services/forum/topics/' + $scope.topicId + "/posts",
            {
                message: postValue
            }).
            success(function(data, status, headers, config)
            {
                $scope.hideAndReset();
                $scope.refreshPosts();
            }).
            error(function(data, status, headers, config)
            {
                // posting failed
            });
    }
});