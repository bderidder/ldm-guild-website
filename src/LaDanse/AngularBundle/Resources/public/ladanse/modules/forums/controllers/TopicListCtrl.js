/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module('LaDanseApp');

app.controller('TopicListCtrl', function ($scope, $rootScope, $location, forumService) {

    $scope.initTopicListCtrl = function(topic)
    {
        $scope.topic = topic;
        $scope.isRecentlyUpdated = false;

        $scope.updateRecentlyUpdated();
    };

    $scope.markTopicAsReadClicked = function()
    {
        console.log('markTopicAsReadClicked');

        forumService.markTopicAsRead($scope.topic.topicId);

        $scope.updateRecentlyUpdated();
    }

    $scope.switchToTopic = function(forumId)
    {
        $location.path('/forums/' + forumId + '/topics/' + $scope.topic.topicId);
    }

    $scope.updateRecentlyUpdated = function()
    {
        forumService.getChangesForUser()
            .then(function(lastChangesModel)
            {
                $scope.isRecentlyUpdated = lastChangesModel.hasTopicChanged($scope.topic.topicId);
            });
    }
});