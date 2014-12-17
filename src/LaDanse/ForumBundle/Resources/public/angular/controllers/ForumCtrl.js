forumControllers.controller('ForumCtrl',
    function ($scope, $routeParams, $rootScope, $http)
    {
        $scope.forumId = $routeParams.forumId;

        $scope.name = '';
        $scope.topics = [];

        $scope.isForumLoaded = false;

        $scope.initForumCtrl = function()
        {
            $scope.refreshTopics();
        };

        $scope.refreshTopics = function()
        {
            $http.get('../services/forum/forums/' + forumId).success(function(data) {
                $scope.name = data.name;
                $scope.topics = data.topics;
                $scope.isForumLoaded = true;
            });
        }

        $scope.currentEdited = {};
        $scope.currentEdited.controller = null
    }
);