var commentsSubApp = angular.module('CommentsSubApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'angularMoment',
        'commentControllers'
    ]
);

var commentControllers = angular.module('commentControllers', ['ngRoute', 'ngResource']);

commentsSubApp.directive('commentEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/comments/partials/CommentEditor.html',
    };
});