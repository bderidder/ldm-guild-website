forumControllers.controller('ForumListCtrl',
    function ($scope, $routeParams, $rootScope, $http)
    {
        $scope.forums = [];
        $scope.isCollapsed = true;

        $scope.initForumListCtrl = function()
        {
            $scope.refreshForums();
        };

        $scope.refreshForums = function()
        {
            $http.get('../services/forum/forums').success(function(data) {
                $scope.forums = data.forums;
            });
        }
    }
);