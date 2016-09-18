/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var charactersSubApp = angular.module('CharactersSubApp',
    [
        'ngRoute',
        'ngResource',
        'ui.bootstrap',
        'angularMoment',
        'charactersControllers'
    ]
);

var charactersControllers = angular.module('charactersControllers', ['ngRoute', 'ngResource']);