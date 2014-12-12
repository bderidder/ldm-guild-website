var forumApp = angular.module('ForumApp',
    ['ngRoute', 'ngResource', 'forumControllers']
);

forumApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                templateUrl: '/bundles/ladanseforum/angular/partials/ForumViews.html',
                controller: 'ForumGroupCtrl'
            });
    }]);