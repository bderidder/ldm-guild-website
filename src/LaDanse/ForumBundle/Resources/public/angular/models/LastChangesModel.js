function LastChangesModel(posts, topics)
{
    this.posts = posts;
    this.topics = topics;

    this.getPostCount = function()
    {
        return this.posts.length;
    }

    this.getPosts = function()
    {
        return this.posts;
    }

    this.hasForumChanged = function(forumId)
    {
        for (i = 0; i < this.posts.length; i++)
        {
            if (this.posts[i].topic.forum.forumId == forumId)
            {
                return true;
            }
        }

        for (i = 0; i < this.topics.length; i++)
        {
            if (this.topics[i].forum.forumId == forumId)
            {
                return true;
            }
        }

        return false;
    }

    this.hasTopicChanged = function(topicId)
    {
        for (i = 0; i < this.posts.length; i++)
        {
            if (this.posts[i].topic.topicId == topicId)
            {
                return true;
            }
        }

        return false;
    }

    this.isTopicNew = function(topicId)
    {
        for (i = 0; i < this.topics.length; i++)
        {
            if (this.topics[i].topicId == topicId)
            {
                return true;
            }
        }

        return false;
    }

    this.isPostNew = function(postId)
    {
        for (i = 0; i < this.posts.length; i++)
        {
            if (this.posts[i].postId == postId)
            {
                return true;
            }
        }

        return false;
    }

    this.markTopicAsRead = function(topicId)
    {
        for (i = 0; i < this.topics.length; i++)
        {
            if (this.topics[i].topicId === topicId)
            {
                this.topics.splice(i, 1);

                return;
            }
        }
    };

    this.markPostAsRead = function(postId)
    {
        for (i = 0; i < this.posts.length; i++)
        {
            if (this.posts[i].postId === postId)
            {
                this.posts.splice(i, 1);

                return;
            }
        }
    };
}