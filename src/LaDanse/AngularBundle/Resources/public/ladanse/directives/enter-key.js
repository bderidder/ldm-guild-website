/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var ladanseApp = angular.module(LADANSE_APP_NAME);

ladanseApp.directive('enterKey', function () {
    return function (scope, element, attrs) {
        element.bind('keydown keypress', function (event) {
            if(event.which === 13) { // 13 = enter key
                scope.$apply(function (){
                    scope.$eval(attrs.enterKey);
                });

                event.preventDefault();
            }
        });
    };
});
