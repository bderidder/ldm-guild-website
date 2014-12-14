forumControllers.controller('TopicCtrl', ['$scope', '$routeParams',
    function($scope, $routeParams) {

        $scope.topicId = $routeParams.topicId;

        $scope.test = "Hello World";

    }]);