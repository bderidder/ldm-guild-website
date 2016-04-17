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
            $http.get('../services/forum/account/unread')
                .success(function(data)
                {
                    forumServiceInstance.changesForUserModel = new LastChangesModel(data.unreadPosts);

                    for (i = 0; i < forumServiceInstance.changesForUserPromises.length; i++)
                    {
                        forumServiceInstance.changesForUserPromises[i].resolve(forumServiceInstance.changesForUserModel);
                    }

                    forumServiceInstance.changesForUserPromises = []
                })
            ;
        }

        forumServiceInstance.markPostAsRead = function(postId)
        {
            forumServiceInstance.changesForUserModel.markPostAsRead(postId);

            $http.get('../services/forum/posts/' + postId + '/markRead');
        }

        forumServiceInstance.markTopicAsRead = function(topicId)
        {
            var posts = forumServiceInstance.changesForUserModel.getPosts();
            var i = 0;

            while(i < posts.length)
            {
                if (posts[i].topic.topicId == topicId)
                {
                    forumServiceInstance.markPostAsRead(posts[i].postId);
                }
                else
                {
                    i++;
                }
            }
        }

        forumServiceInstance.fetchActivityData();
        forumServiceInstance.fetchChangesForUser();

        return forumServiceInstance;
});