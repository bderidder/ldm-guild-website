var commentsApp = angular.module('CommentsApp',
    ['ngRoute', 'ngResource', 'commentControllers']
);

commentsApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                templateUrl: '/bundles/ladansecomment/angular/partials/CommentsViews.html',
                controller: 'CommentGroupCtrl'
            });
    }]);