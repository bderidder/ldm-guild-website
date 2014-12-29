forumControllers.controller('TopicListCtrl', function ($scope, $routeParams, $rootScope, forumService) {

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

    $scope.updateRecentlyUpdated = function()
    {
        forumService.getChangesForUser()
            .then(function(lastChangesModel)
            {
                $scope.isRecentlyUpdated = lastChangesModel.hasTopicChanged($scope.topic.topicId);
            });
    }
});