/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var STATIC_PAGES_MODULE_NAME = 'StaticPages';

var ladanseApp = GetLaDanseApp();

var staticPagesModule = CreateAngularModule(STATIC_PAGES_MODULE_NAME);

staticPagesModule.config(
    ['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider)
        {
            $stateProvider
                .state('static', {
                    url: '/static',
                    abstract: true,
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/static/pages/Static.html')
                })
                .state('static.about', {
                    url: '/about',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/static/pages/About.html')
                });
        }
    ]
);