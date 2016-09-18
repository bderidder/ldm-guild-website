/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var rosterSubApp = angular.module('RosterSubApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'angularMoment',
        'rosterControllers'
    ]
);

var rosterControllers = angular.module('rosterControllers', ['ngRoute', 'ngResource']);