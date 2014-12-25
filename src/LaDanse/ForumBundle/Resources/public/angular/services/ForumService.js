forumApp.service(
    'forumService',
    function($http, $log, $q)
    {
        var forumServiceInstance = {};

        forumServiceInstance.activityModel = null;
        forumServiceInstance.activityPromises = [];

        forumServiceInstance.getLastActivity = function()
        {
            var deferred = $q.defer();

            if (forumServiceInstance.activityModel === null)
            {
                forumServiceInstance.activityPromises.push(deferred);
            }
            else
            {
                deferred.resolve(forumServiceInstance.activityModel);
            };

            return deferred.promise;
        };

        forumServiceInstance.fetchActivityData = function()
        {
            $http.get('../services/forum/forums/activity')
                .success(function(data)
                {
                    forumServiceInstance.activityModel = new ActivityModel(data.posts);

                    for (i = 0; i < forumServiceInstance.activityPromises.length; i++)
                    {
                        forumServiceInstance.activityPromises[i].resolve(forumServiceInstance.activityModel);
                    }

                    forumServiceInstance.activityPromises = []
                })
            ;
        }

        forumServiceInstance.fetchActivityData();

        return forumServiceInstance;
});