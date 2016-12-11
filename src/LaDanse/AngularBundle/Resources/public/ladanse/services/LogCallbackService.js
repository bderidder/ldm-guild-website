/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module(LADANSE_APP_NAME);

app.service(
    'logCallbackService',
    function($http, $window)
    {
        var serviceInstance = {};

        serviceInstance.log = function(source, message)
        {
            $http.post(
                Routing.generate('logCallbackAction'),
                {
                    'source': source,
                    'message': message
                })
                .then(
                    function()
                    {
                        // nothing to do if there was success
                    },
                    function()
                    {
                        console.log("Failed to log - " + source + " (accountId " + $window.currentAccount.id + ") - " + message);
                    }
                );
        };

        return serviceInstance;
    });
