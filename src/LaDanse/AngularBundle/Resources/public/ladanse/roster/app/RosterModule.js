/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var ROSTER_MODULE_NAME = 'Roster';

var ladanseApp = GetLaDanseApp();

var rosterModule = CreateAngularModule(ROSTER_MODULE_NAME);

rosterModule.config(
    ['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider)
        {
            $stateProvider
                .state('roster', {
                    url: '/roster',
                    abstract: true,
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/roster/partials/Roster.html'
                })
                .state('roster.home', {
                    url: '',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/roster/partials/RosterView.html'
                });
        }
    ]
);