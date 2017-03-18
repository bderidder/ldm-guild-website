/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

'use strict';

var LADANSE_APP_NAME = "LaDanseApp";

var ladanseApp = angular.module(LADANSE_APP_NAME,
    [
        'LaDanseApp.Forums',
        'LaDanseApp.Characters',
        'LaDanseApp.Comments',
        'LaDanseApp.Roster',
        'LaDanseApp.Events',
        'ngResource',
        'ui.bootstrap',
        'ngSanitize',
        'textAngular',
        'ngAnimate',
        'ui.router',
        'angularMoment',
        'angular-inview',
        'monospaced.elastic',
        'ui.select'
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
        function ($rootScope, $state, $stateParams)
        {
            $rootScope.$state = $state;
            $rootScope.$stateParams = $stateParams;
        }
    ]
);

ladanseApp.config(
    [   '$stateProvider', '$urlRouterProvider', '$locationProvider',
        function ($stateProvider, $urlRouterProvider, $locationProvider)
        {
            $locationProvider.html5Mode(false);
            $locationProvider.hashPrefix('');

            $urlRouterProvider.otherwise(function($injector)
            {
                var fullUrl = window.location.href;

                if (fullUrl.includes('/forum'))
                {
                    return '/forums';
                }
                else if (fullUrl.includes('/eventsDisabled'))
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
                else if (fullUrl.includes('/events'))
                {
                    return '/events';
                }

                return "/menu";
            });

            $stateProvider.state(
                "menu",
                {
                    url: "/menu",
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/route/ShouldNeverHappen.html'
                }
            )
        }
    ]
)
