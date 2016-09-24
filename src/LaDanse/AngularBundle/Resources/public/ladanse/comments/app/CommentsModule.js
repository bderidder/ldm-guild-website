var COMMENTS_MODULE_NAME = 'Comments';

var ladanseApp = GetLaDanseApp();

var commentsModule = CreateAngularModule(COMMENTS_MODULE_NAME);

commentsModule.config(
    ['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider)
        {
            $stateProvider
                .state('comments', {
                    url: '/comments',
                    abstract: true,
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/comments/partials/Comments.html'
                })
                .state('comments.home', {
                    url: '',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/comments/partials/CommentsView.html'
                });
        }
    ]
);

commentsModule.directive('commentEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/comments/partials/CommentEditor.html',
    };
});