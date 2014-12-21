forumControllers.controller('ActivitySliderCtrl',
    function($scope, $rootScope, $http, $timeout)
    {
        $scope.counter = {};
        $scope.active = true;

        $scope.initActivitySliderCtrl = function(dataUrl)
        {
            $scope.counter.value = 0;

            $scope.fetchData(dataUrl);

            $scope.$on("$destroy", function()
            {
                $scope.active = false;
            });
        };

        $scope.isCurrentSlideIndex = function(index)
        {
            return $scope.counter.value == index;
        }

        $scope.advanceValue = function()
        {
            $scope.counter.value = $scope.counter.value + 1;

            if ($scope.counter.value >= $scope.itemCount)
            {
                $scope.counter.value = 0;
            }

            if ($scope.active)
            {
                $timeout(function()
                {
                    $scope.advanceValue();
                }, 3500);
            }
        }

        $scope.fetchData = function(dataUrl)
        {
            $http.get(dataUrl).success(function(data) {
                $scope.itemCount = data.posts.length;
                $scope.posts = data.posts;
                $scope.advanceValue();
            });
        }
    }
);