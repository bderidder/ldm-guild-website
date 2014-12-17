forumControllers.controller('TopicListCtrl', function ($scope, $routeParams, $rootScope, $http) {

    $scope.initTopicListCtrl = function(topic)
    {
        $scope.topic = topic;
    };
});