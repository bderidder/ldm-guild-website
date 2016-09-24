var LADANSE_APP_NAME = "LaDanseApp";

var ladanseApp = angular.module(LADANSE_APP_NAME,
    [
        'LaDanseApp.Forums',
        'LaDanseApp.Characters',
        'LaDanseApp.Comments',
        'LaDanseApp.Roster',
        'ngResource',
        'ui.bootstrap',
        'ngSanitize',
        'textAngular',
        'ngAnimate',
        'ui.router',
        'angularMoment',
        'angular-inview',
        'monospaced.elastic'
    ]
);

function GetLaDanseApp()
{
    return angular.module(LADANSE_APP_NAME);
}

function GetFQAngularModuleName(moduleName)
{
    return 'LaDanseApp.' + moduleName;
}

function CreateAngularModule(moduleName)
{
    return angular.module(GetFQAngularModuleName(moduleName),
        [
            'ui.router'
        ]
    );
}

function GetAngularModule(moduleName)
{
    return angular.module(GetFQAngularModuleName(moduleName));
}

ladanseApp.run(
    [   '$rootScope', '$state', '$stateParams',
        function ($rootScope,   $state,   $stateParams)
        {
            $rootScope.$state = $state;
            $rootScope.$stateParams = $stateParams;
        }
    ]
);

ladanseApp.config(
    [   '$stateProvider', '$urlRouterProvider',
        function ($stateProvider,   $urlRouterProvider)
        {
            $urlRouterProvider.otherwise(function($injector, $location)
            {
                var fullUrl = $location.absUrl();

                if (fullUrl.includes('/forum'))
                {
                    return '/forums';
                }
                else if (fullUrl.includes('/events'))
                {
                    return '/comments';
                }
                else if (fullUrl.includes('/characters'))
                {
                    return '/characters';
                }
                else if (fullUrl.includes('/roster'))
                {
                    return '/roster';
                }

                return "/ShouldNeverHappen";
            });

            $stateProvider.state(
                "ShouldNeverHappen",
                {
                    url: "/ShouldNeverHappen",
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/route/ShouldNeverHappen.html'
                }
            )
        }
    ]
)

/*
ladanseApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/bundles/ladanseangular/ladanse/app/RedirectView.html',
                controller: 'RedirectCtrl'
            })
            .when('/comments', {
                templateUrl: '/bundles/ladanseangular/ladanse/comments/partials/CommentsViews.html',
                controller: 'CommentGroupCtrl'
            })
            .when('/characters', {
                templateUrl: '/bundles/ladanseangular/ladanse/characters/partials/CharactersView.html',
                controller: 'CharactersViewCtrl'
            })
            .when('/roster', {
                templateUrl: '/bundles/ladanseangular/ladanse/roster/partials/RosterView.html',
                controller: 'RosterViewCtrl'
            }
        );
    }]);
    */