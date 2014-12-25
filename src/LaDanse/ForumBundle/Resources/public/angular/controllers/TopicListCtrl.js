forumControllers.controller('TopicListCtrl', function ($scope, $routeParams, $rootScope, forumService) {

    $scope.initTopicListCtrl = function(topic)
    {
        $scope.topic = topic;
        $scope.isRecentlyUpdated = false;

        forumService.getChangesForUser()
            .then(function(activityModel)
            {
                $scope.isRecentlyUpdated = activityModel.isTopicInActivity($scope.topic.topicId);
            });
    };
});