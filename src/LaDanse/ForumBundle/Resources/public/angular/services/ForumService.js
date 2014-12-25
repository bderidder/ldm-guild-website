forumApp.service(
    'forumService',
    function($http, $log, $q)
    {
        var forumServiceInstance = {};

        forumServiceInstance.activityModel = null;
        forumServiceInstance.activityPromises = [];

        forumServiceInstance.changesForUserModel = null;
        forumServiceInstance.changesForUserPromises = [];

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

        forumServiceInstance.getChangesForUser = function()
        {
            var deferred = $q.defer();

            if (forumServiceInstance.changesForUserModel === null)
            {
                forumServiceInstance.changesForUserPromises.push(deferred);
            }
            else
            {
                deferred.resolve(forumServiceInstance.changesForUserModel);
            };

            return deferred.promise;
        };

        forumServiceInstance.fetchChangesForUser = function()
        {
            $http.get('../services/forum/account/changesForAccount')
                .success(function(data)
                {
                    forumServiceInstance.changesForUserModel = new LastChangesModel(data.newPosts, data.newTopics);

                    for (i = 0; i < forumServiceInstance.changesForUserPromises.length; i++)
                    {
                        forumServiceInstance.changesForUserPromises[i].resolve(forumServiceInstance.changesForUserModel);
                    }

                    forumServiceInstance.changesForUserPromises = []
                })
            ;
        }

        forumServiceInstance.fetchActivityData();
        forumServiceInstance.fetchChangesForUser();

        return forumServiceInstance;
});