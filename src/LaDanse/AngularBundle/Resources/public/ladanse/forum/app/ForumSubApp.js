var forumSubApp = angular.module('ForumSubApp',
    [
        'ngRoute',
        'ngResource'
    ]
);

forumSubApp.directive('postEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/PostEditor.html',
    };
});

forumSubApp.directive('headerTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/HeaderView.html',
    };
});

forumSubApp.directive('forumItemTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/ForumItemView.html',
    };
});

forumSubApp.animation('.slide-animation', function()
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

forumSubApp.directive('escKey', function () {
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

forumSubApp.directive('enterKey', function () {
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