forumControllers.controller('TopicListCtrl', function ($scope, $routeParams, $rootScope, forumService) {

    $scope.initTopicListCtrl = function(topic)
    {
        $scope.topic = topic;
        $scope.isRecentlyUpdated = false;

        forumService.getChangesForUser()
            .then(function(lastChangesModel)
            {
                $scope.isRecentlyUpdated =
                    lastChangesModel.isTopicNew($scope.topic.topicId)
                    ||
                    lastChangesModel.hasTopicChanged($scope.topic.topicId);
            });
    };
});