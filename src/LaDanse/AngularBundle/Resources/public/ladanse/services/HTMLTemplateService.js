
(function()
{
    "use strict";

    GetLaDanseApp().service(
        'HTMLTemplateService',
        HTMLTemplateService
    );

    HTMLTemplateService.$inject = ['$http', '$q', '$compile'];

    function HTMLTemplateService($http, $q, $compile)
    {
        var htmlTemplateService = {};

        var templateCache = BoomerangCache.create('HTMLTemplateService.templateCache');
        templateCache.clear();

        htmlTemplateService.getCompiledTemplate = getCompiledTemplate;

        function getCompiledTemplate(scope, templateUrl, viewModel)
        {
            var deferred = $q.defer();

            try {
                $q.all([
                    getTemplate(templateUrl)
                ]).then(
                    function (data) {
                        var template = data[0];

                        var viewModelScope = scope.$new(true);

                        viewModelScope.viewModel = viewModel;

                        var content = $compile(template)(viewModelScope);

                        deferred.resolve(content);
                    }
                ).catch(
                    function (data) {
                        console.log("Failed to get all data for HTMLTemplateService");
                        console.log(data);
                        deferred.reject("Failed to get all data for HTMLTemplateService");
                    }
                ).finally(
                    function () {
                    }
                );
            }
            catch (e) {
                console.log(e);
            }

            return deferred.promise;
        }

        function getTemplate(templateUrl)
        {
            return getCachedOrFetch(
                templateUrl,
                templateCache,
                60 * 60,
                function () {
                    return $http.get(templateUrl);
                },
                function (httpResponse) {
                    return httpResponse.data;
                }
            );
        }

        function getCachedOrFetch(cacheKey, cache, cacheTTL, fetcherFunction, extractorFunction)
        {
            var deferred = $q.defer();

            var cachedObject = cache.get(cacheKey);

            if (cachedObject !== null)
            {
                deferred.resolve(cachedObject);
            }
            else
            {
                try
                {
                    fetcherFunction()
                        .then(
                            function (fetchedObject) {
                                var objectToCache = fetchedObject;

                                if (extractorFunction) {
                                    objectToCache = extractorFunction(fetchedObject);
                                }

                                cache.set(cacheKey, objectToCache, cacheTTL);

                                deferred.resolve(objectToCache);
                            },
                            function (error) {
                                console.log(error);
                                deferred.reject('Failed to fetch object');
                            }
                        );
                }
                catch (e) {
                    console.log(e);
                }
            }

            return deferred.promise;
        }

        return htmlTemplateService;
    }
})();