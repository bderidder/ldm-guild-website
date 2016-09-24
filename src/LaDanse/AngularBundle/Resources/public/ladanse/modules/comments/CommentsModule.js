/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

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
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/modules/comments/partials/Comments.html'
                })
                .state('comments.home', {
                    url: '',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/modules/comments/partials/CommentsView.html'
                });
        }
    ]
);

commentsModule.directive('commentEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/modules/comments/partials/CommentEditor.html',
    };
});