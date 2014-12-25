function ActivityModel(posts)
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

    this.isForumInActivity = function(forumId)
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

    this.isTopicInActivity = function(topicId)
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

    this.isPostInActivity = function(postId)
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
}