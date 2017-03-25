/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

(function() {

    "use strict";

    var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

    eventsModule
        .directive('ladanseTime', function () {
            return {
                restrict: 'E',
                controller: 'LaDanseTimeCtrl',
                controllerAs: 'ctrl',
                scope: {
                    time: '='
                },
                templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/ladanseTime/ladanseTime.html')
            };
        })
        .controller('LaDanseTimeCtrl', LaDanseTimeCtrl);

    LaDanseTimeCtrl.$inject = ['$scope'];

    function LaDanseTimeCtrl($scope)
    {
        var ctrl = this;

        var localTimeZone = moment.tz.guess();
        //var localTimeZone = "Africa/Johannesburg";

        ctrl.format = 'HH:mm';

        ctrl.times = [
            {
                'label': 'Server Realm Time',
                'time': moment($scope.time).tz(Constants.REALM_SERVER_TIMEZONE).format(ctrl.format),
                'timeZone': Constants.REALM_SERVER_TIMEZONE
            },
            {
                'label': 'Local Time',
                'time': moment($scope.time).tz(localTimeZone).format(ctrl.format),
                'timeZone': localTimeZone
            }
        ];

        ctrl.time = moment($scope.time).tz(Constants.REALM_SERVER_TIMEZONE);
    }
})();
