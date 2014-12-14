var forumApp = angular.module('ForumApp',
    ['ngRoute', 'ngResource', 'forumControllers']
);

var forumControllers = angular.module('forumControllers', ['ngRoute', 'ngResource']);

forumApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/bundles/ladanseforum/angular/partials/ForumViews.html',
                controller: 'ForumGroupCtrl'
            })
            .when('/topic/:TopicId', {
                templateUrl: '/bundles/ladanseforum/angular/partials/TopicView.html',
                controller: 'TopicCtrl'
            });
    }]);

forumApp.directive('escKey', function () {
    return function (scope, element, attrs) {
        element.bind('keydown keypress', function (event) {
            if(event.which === 27) { // 27 = esc key
                scope.$apply(function (){
                    scope.$eval(attrs.escKey);
                });

                event.preventDefault();
            }
        });
    };
})

forumApp.directive('enterKey', function () {
    return function (scope, element, attrs) {
        element.bind('keydown keypress', function (event) {
            if(event.which === 13) { // 13 = enter key
                scope.$apply(function (){
                    scope.$eval(attrs.enterKey);
                });

                event.preventDefault();
            }
        });
    };
})