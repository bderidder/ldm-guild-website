var forumApp = angular.module('ForumApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'ngSanitize',
        'textAngular',
        'ngAnimate',
        'angularMoment',
        'forumControllers'
    ]
);

var forumControllers = angular.module('forumControllers', ['ngRoute', 'ngResource']);

forumApp.directive('postEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseforum/angular/partials/PostEditor.html',
    };
});

forumApp.directive('headerTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseforum/angular/partials/HeaderView.html',
    };
});

forumApp.directive('forumItemTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseforum/angular/partials/ForumItemView.html',
    };
});

forumApp.animation('.slide-animation', function()
{
    return {
        addClass: function (element, className, done)
        {
            if (className == 'ng-hide')
            {
                TweenMax.to(element, 0.5, {left: -element.parent().width(), onComplete: done});
            }
            else
            {
                done();
            }
        },
        removeClass: function (element, className, done)
        {
            if (className == 'ng-hide')
            {
                TweenMax.set(element, {left: element.parent().width()});
                TweenMax.to(element, 0.5, {left: 0, onComplete: done});
            }
            else
            {
                done();
            }
        }
    }
});

forumApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/bundles/ladanseforum/angular/partials/ForumListView.html',
                controller: 'ForumListCtrl'
            })
            .when('/latestposts', {
                templateUrl: '/bundles/ladanseforum/angular/partials/LatestPostsView.html',
                controller: 'LatestPostsPageCtrl'
            })
            .when('/:forumId', {
                templateUrl: '/bundles/ladanseforum/angular/partials/ForumView.html',
                controller: 'ForumPageCtrl'
            })
            .when('/:forumId/topics/:topicId', {
                templateUrl: '/bundles/ladanseforum/angular/partials/TopicView.html',
                controller: 'TopicPageCtrl'
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