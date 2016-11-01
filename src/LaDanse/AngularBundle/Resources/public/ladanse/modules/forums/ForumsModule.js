/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

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
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/partials/Forums.html')
                })
                .state('forums.list', {
                    url: '',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/partials/ForumListView.html')
                })
                .state('forums.latestposts', {
                    url: '/latestposts',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/partials/LatestPostsView.html')
                })
                .state('forums.forum', {
                    url: '/{forumId}',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/partials/ForumView.html')
                })
                .state('forums.topic', {
                    url: '/{forumId}/topics/{topicId}',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/partials/TopicView.html')
                });
        }
    ]
);

forumsModule.animation('.slide-animation', function()
{
    return {
        addClass: function (element, className, done)
        {
            if (className == 'ng-hide')
            {
                TweenMax.to(element, 0.5, {left: -1 * $(element.parent()).width(), onComplete: done});
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
                TweenMax.set(element, {left: $(element.parent()).width()});
                TweenMax.to(element, 0.5, {left: 0, onComplete: done});
            }
            else
            {
                done();
            }
        }
    }
});