var commentsApp = angular.module('CommentsApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'angularMoment',
        'commentControllers'
    ]
);

commentsApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                templateUrl: '/bundles/ladansecomment/angular/partials/CommentsViews.html',
                controller: 'CommentGroupCtrl'
            });
    }]
);

var commentControllers = angular.module('commentControllers', ['ngRoute', 'ngResource']);

commentsApp.directive('commentEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladansecomment/angular/partials/CommentEditor.html',
    };
});

commentsApp.directive('escKey', function () {
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

commentsApp.directive('enterKey', function () {
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