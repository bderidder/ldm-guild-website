function LastChangesModel(posts)
{
    this.posts = posts;

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

    this.markForumAsRead = function(topicId)
    {
    }

    this.markTopicAsRead = function(topicId)
    {
    }

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