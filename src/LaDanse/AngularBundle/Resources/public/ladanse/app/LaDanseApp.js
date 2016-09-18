var ladanseApp = angular.module('LaDanseApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'ngSanitize',
        'textAngular',
        'ngAnimate',
        'angularMoment',
        'angular-inview',
        'ForumSubApp',
        'CommentsSubApp',
        'CharactersSubApp',
        'RosterSubApp'
    ]
);

ladanseApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/bundles/ladanseangular/ladanse/app/RedirectView.html',
                controller: 'RedirectCtrl'
            })
            .when('/forums', {
                templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/ForumListView.html',
                controller: 'ForumListCtrl'
            })
            .when('/forums/latestposts', {
                templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/LatestPostsView.html',
                controller: 'LatestPostsPageCtrl'
            })
            .when('/forums/:forumId', {
                templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/ForumView.html',
                controller: 'ForumPageCtrl'
            })
            .when('/forums/:forumId/topics/:topicId', {
                templateUrl: '/bundles/ladanseangular/ladanse/forum/partials/TopicView.html',
                controller: 'TopicPageCtrl'
            })
            .when('/comments', {
                templateUrl: '/bundles/ladanseangular/ladanse/comments/partials/CommentsViews.html',
                controller: 'CommentGroupCtrl'
            })
            .when('/characters', {
                templateUrl: '/bundles/ladanseangular/ladanse/characters/partials/CharactersView.html',
                controller: 'CharactersViewCtrl'
            }).when('/roster', {
            templateUrl: '/bundles/ladanseangular/ladanse/roster/partials/RosterView.html',
            controller: 'RosterViewCtrl'
        });
    }]);

ladanseApp.controller('RedirectCtrl', function ($scope, $location)
{
    var fullUrl = $location.absUrl();

    if (fullUrl.includes('/forum'))
    {
        $location.path('/forums');
    }
    else if (fullUrl.includes('/events'))
    {
        $location.path('/comments');
    }
    else if (fullUrl.includes('/characters'))
    {
        $location.path('/characters');
    }
    else if (fullUrl.includes('/roster'))
    {
        $location.path('/roster');
    }
});