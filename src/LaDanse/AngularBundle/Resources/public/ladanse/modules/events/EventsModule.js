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
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/abstract.html')
                })
                .state('events.calendar', {
                    url: '',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/calendar/calendar.html')
                })
                .state('events.event', {
                    url: '/event',
                    abstract: true,
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/abstract.html')
                })
                .state('events.event.view', {
                    url: '/{eventId:[0-9]+}',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/view.html')
                })
                .state('events.event.create', {
                    url: '/create',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/create.html')
                })
                .state('events.event.edit', {
                    url: '/{eventId:[0-9]+}/edit',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/edit.html')
                })
                .state('events.event.signup', {
                    url: '/{eventId:[0-9]+}/signup',
                    abstract: true,
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/signup/abstract.html')
                })
                .state('events.event.signup.create', {
                    url: '/create',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/signup/create.html')
                })
                .state('events.event.signup.edit', {
                    url: '/{signUpId:[0-9]+}',
                    templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/partials/event/signup/edit.html')
                });
        }
    ]
);