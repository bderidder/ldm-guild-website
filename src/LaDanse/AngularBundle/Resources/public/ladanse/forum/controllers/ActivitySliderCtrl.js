/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumSubApp.controller('ActivitySliderCtrl',
    function($scope, $rootScope, $timeout, forumService)
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

        $scope.startSlider = function()
        {
            $scope.active = true;
            $scope.counter.value = 0;

            $timeout(function()
            {
                $scope.advanceValue();
            }, 4500);
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
                }, 4500);
            }
        }

        $scope.fetchData = function(dataUrl)
        {
            forumService.getLastActivity()
                .then(function(activityModel)
                {
                    $scope.itemCount = activityModel.getPostCount();
                    $scope.posts = activityModel.getPosts();
                    $scope.startSlider();
                });
        }
    }
);