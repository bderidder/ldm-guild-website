forumControllers.controller('HeaderCtrl',
    function($scope, $routeParams, $rootScope, $http)
    {
        $scope.initHeaderCtrl = function(headerText, headerURL)
        {
            $scope.headerText = headerText;
            $scope.headerURL = headerURL;
        };
    }
);