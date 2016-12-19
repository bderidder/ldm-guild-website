/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var EVENTS_MODULE_NAME = 'Events';

var ladanseApp = GetLaDanseApp();

var eventsModule = CreateAngularModule(EVENTS_MODULE_NAME);

eventsModule.config(
    ['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider)
        {
            $stateProvider
                .state('events', {
                    url: '/events',
                    abstract: true,
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/Events.html')
                })
                .state('events.home', {
                    url: '',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/EventsView.html')
                });
        }
    ]
);