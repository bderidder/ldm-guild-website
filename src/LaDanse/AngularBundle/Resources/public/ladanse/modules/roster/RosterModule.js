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
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/partials/Roster.html')
                })
                .state('roster.home', {
                    url: '?criteria',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/partials/RosterView.html')
                })
                .state('roster.member', {
                    url: '/{accountId}',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/partials/RosterMember.html')
                });
        }
    ]
);