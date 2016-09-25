/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var app = angular.module(LADANSE_APP_NAME);

app.service(
    'gameDataService',
    function($http, $log, $q)
    {
        var serviceInstance = {};

        serviceInstance.guilds = null;
        serviceInstance.realms = null;
        serviceInstance.gameRaces = null;
        serviceInstance.gameClasses = null;
        serviceInstance.gameFactions = null;

        serviceInstance.gameData = null;

        serviceInstance.gameDataPromises = [];

        serviceInstance.getGameData = function()
        {
            var deferred = $q.defer();

            if (serviceInstance.gameData === null)
            {
                serviceInstance.gameDataPromises.push(deferred);
            }
            else
            {
                deferred.resolve(serviceInstance.gameData);
            };

            return deferred.promise;
        };

        serviceInstance.fetchGameData = function()
        {
            $http.get(Routing.generate('getAllGuilds'))
                .success(function(data)
                {
                    serviceInstance.guilds = data;

                    serviceInstance.verifyPromises();
                });

            $http.get(Routing.generate('getAllRealms'))
                .success(function(data)
                {
                    serviceInstance.realms = data;

                    serviceInstance.verifyPromises();
                });

            $http.get(Routing.generate('getAllGameClasses'))
                .success(function(data)
                {
                    serviceInstance.gameClasses = data;

                    serviceInstance.verifyPromises();
                });
            $http.get(Routing.generate('getAllGameRaces'))
                .success(function(data)
                {
                    serviceInstance.gameRaces = data;

                    serviceInstance.verifyPromises();
                });
            $http.get(Routing.generate('getAllGameFactions'))
                .success(function(data)
                {
                    serviceInstance.gameFactions = data;

                    serviceInstance.verifyPromises();
                });
        }

        serviceInstance.verifyPromises = function()
        {
            if (serviceInstance.isAllGameDataLoaded())
            {
                serviceInstance.gameData = new GameDataModel(
                    serviceInstance.guilds,
                    serviceInstance.realms,
                    serviceInstance.gameRaces,
                    serviceInstance.gameClasses,
                    serviceInstance.gameFactions
                );

                for (i = 0; i < serviceInstance.gameDataPromises.length; i++)
                {
                    serviceInstance.gameDataPromises[i].resolve(serviceInstance.gameData);
                }

                serviceInstance.gameDataPromises = []
            }
        }

        serviceInstance.isAllGameDataLoaded = function()
        {
            return !(
                serviceInstance.guilds === null
                || serviceInstance.realms === null
                || serviceInstance.gameRaces === null
                || serviceInstance.gameClasses === null
                || serviceInstance.gameFactions === null
            );
        }

        serviceInstance.fetchGameData();

        return serviceInstance;
    });
