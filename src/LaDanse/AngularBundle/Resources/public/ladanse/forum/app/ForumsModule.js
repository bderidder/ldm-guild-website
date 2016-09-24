var FORUMS_MODULE_NAME = 'Forums';

var ladanseApp = GetLaDanseApp();

var forumsModule = CreateAngularModule(FORUMS_MODULE_NAME);

forumsModule.config(
    [   '$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider) {

            $stateProvider
                .state('forums', {
                    url: '/forums',
                    abstract: true,
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/forum/partials/Forums.html'
                })
                .state('forums.list', {
                    url: '',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/forum/partials/ForumListView.html'
                })
                .state('forums.latestposts', {
                    url: '/latestposts',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/forum/partials/LatestPostsView.html'
                })
                .state('forums.forum', {
                    url: '/{forumId}',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/forum/partials/ForumView.html'
                })
                .state('forums.topic', {
                    url: '/{forumId}/topics/{topicId}',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/forum/partials/TopicView.html'
                });
        }
    ]
);

forumsModule.directive('postEditorTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/PostEditor.html',
    };
});

forumsModule.directive('headerTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/HeaderView.html',
    };
});

forumsModule.directive('forumItemTemplate', function() {
    return {
        templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/ForumItemView.html',
    };
});

forumsModule.animation('.slide-animation', function()
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