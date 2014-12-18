forumControllers.controller('ForumListCtrl',
    function ($scope, $routeParams, $rootScope, $http)
    {
        $scope.initForumListCtrl = function()
        {
            $scope.refreshTopics();
        };

        $scope.getForum = function(forumId)
        {
            var arrayLength = $scope.forums.length;
            for (var i = 0; i < arrayLength; i++)
            {
                var forum = $scope.forums[i];

                if (forum.forumId == forumId)
                {
                    return forum;
                }
            }

            return null;
        }

        $scope.refreshTopics = function()
        {
            $http.get('../services/forum/forums').success(function(data) {
                $scope.forums = data.forums;
                $scope.topics = data.topics;
                $scope.isForumLoaded = true;
            });
        }
    }
);