"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.service(
    'claimTooltipService',
    function($http, $log, $q, $compile, eventService, characterService, gameDataService)
    {
        var claimTooltipServiceInstance = {};

        claimTooltipServiceInstance.getTooltipHTML = function(scope, eventId, accountId)
        {
            var deferred = $q.defer();

            var myThis = this;

            try
            {
                $q.all([
                    this._getTemplate(),
                    this._getEventById(eventId),
                    this._getCharactersClaimedByAccount(accountId),
                    gameDataService.getGameData()
                ]).then(
                    function(data)
                    {
                        var template  = data[0];
                        var eventDto  = data[1];
                        var claimsDto = data[2];
                        var gameData  = data[3];

                        var viewModel = myThis._getViewModel(scope, accountId, eventDto, claimsDto, gameData);

                        var content = $compile(template)(viewModel);

                        deferred.resolve(content);
                    }
                ).catch(
                    function(data)
                    {
                        console.log("Failed to get all data for claim tooltip");
                        console.log(data);
                        deferred.reject('Failed to get events');
                    }
                ).finally(
                    function ()
                    {
                    }
                );
            }
            catch (e)
            {
                console.log(e);
            }

            return deferred.promise;
        };

        claimTooltipServiceInstance._getViewModel = function(scope, accountId, eventDto, claimsDto, gameData)
        {
            var claimScope = scope.$new(true);

            var signUpDto = this._getSignUpForAccountId(eventDto, accountId);

            if (signUpDto == null)
            {
                claimScope.error = "Encountered an error while trying to get claims for this account";
                return;
            }

            claimScope.claims = [];

            for(var i = 0; i < claimsDto.length; i++)
            {
                var currentClaimDto = claimsDto[i];

                if (currentClaimDto.level != 50)
                    continue;

                if (!this._atLeastOneRoleMatches(signUpDto.roles, currentClaimDto.claim.roles))
                    continue;

                var claimTip = new ClaimTip.ClaimTipModel();

                claimTip.name = currentClaimDto.name;
                claimTip.raider = currentClaimDto.claim.raider;
                claimTip.race = gameData.getGameRace(currentClaimDto.gameRaceReference.id).name;
                claimTip.realm = gameData.getRealm(currentClaimDto.realmReference.id).name;
                claimTip.class = gameData.getGameClass(currentClaimDto.gameClassReference.id).name;
                claimTip.level = currentClaimDto.level;
                claimTip.roles = currentClaimDto.claim.roles;

                claimScope.claims.push(claimTip);
            }

            return claimScope;
        };

        claimTooltipServiceInstance._getTemplate = function()
        {
            var templateUrl = Assetic.generate('/ladanseangular/ladanse/modules/events/directives/qtipClaim/claimTooltip.html');

            return this._getCachedOrFetch(
                templateUrl,
                this._templateCache,
                60 * 60,
                function()
                {
                    return $http.get(templateUrl);
                },
                function(httpResponse)
                {
                    return httpResponse.data;
                }
            );
        };

        claimTooltipServiceInstance._getEventById = function(eventId)
        {
            return this._getCachedOrFetch(
                eventId,
                this._eventsCache,
                60,
                function()
                {
                    return eventService.getEventById(eventId);
                }
            );
        };

        claimTooltipServiceInstance._getCharactersClaimedByAccount = function(accountId)
        {
            return this._getCachedOrFetch(
                accountId,
                this._claimsCache,
                60,
                function()
                {
                    return characterService.getCharactersClaimedByAccount(accountId);
                }
            );
        };

        claimTooltipServiceInstance._getCachedOrFetch = function(cacheKey, cache, cacheTTL, fetcherFunction, extractorFunction)
        {
            var deferred = $q.defer();

            var cachedObject = cache.get(cacheKey);

            if (cachedObject != null)
            {
                deferred.resolve(cachedObject);
            }
            else
            {
                try
                {
                    fetcherFunction()
                        .then(
                            function(fetchedObject)
                            {
                                var objectToCache = fetchedObject;

                                if (extractorFunction)
                                {
                                    objectToCache = extractorFunction(fetchedObject);
                                }

                                cache.set(cacheKey, objectToCache, cacheTTL);

                                deferred.resolve(objectToCache);
                            },
                            function(error)
                            {
                                console.log(error);
                                deferred.reject('Failed to fetch object');
                            }
                        );
                }
                catch (e)
                {
                    console.log(e);
                }
            }

            return deferred.promise;
        };

        claimTooltipServiceInstance._getSignUpForAccountId = function(eventDto, accountId)
        {
            for(var i = 0; i < eventDto.signUps.length; i++)
            {
                var currentSignUp = eventDto.signUps[i];

                if (currentSignUp.accountRef.id == accountId)
                    return currentSignUp;
            }

            return null;
        };

        claimTooltipServiceInstance._atLeastOneRoleMatches = function(signUpRoles, claimedRoles)
        {
            var commonRoles = signUpRoles.filter(
                function(n)
                {
                    return claimedRoles.indexOf(n) !== -1;
                }
            );


            return commonRoles.length > 0;
        };

        claimTooltipServiceInstance._init = function()
        {
            this._templateCache = BoomerangCache.create('templateCache');
            this._eventsCache = BoomerangCache.create('eventsCache');
            this._claimsCache = BoomerangCache.create('claimsCache');
        };

        claimTooltipServiceInstance._init();

        return claimTooltipServiceInstance;
    });