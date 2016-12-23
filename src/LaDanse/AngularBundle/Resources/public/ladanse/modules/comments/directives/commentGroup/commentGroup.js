/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('commentGroup', function()
{
    return {
        restrict: 'E',
        controller: 'CommentGroupCtrl',
        controllerAs: 'ctrl',
        scope: {
            commentGroupId: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/comments/directives/commentGroup/commentGroup.html')
    };
})
.controller('CommentGroupCtrl', function($scope, $rootScope, $stateParams, $http)
{
    var ctrl = this;

    ctrl.comments = [];
    ctrl.groupId = 0;

    ctrl.initCommentGroupCtrl = function()
    {
        ctrl.groupId = $scope.commentGroupId;

        ctrl.refreshPosts();

        var unbindAddSucceeded =
            $rootScope.$on('CommentsApp.AddComment.Succeeded', ctrl.refreshPosts);
        var unbindEditSucceeded =
            $rootScope.$on('CommentsApp.EditComment.Succeeded', ctrl.refreshPosts);

        $scope.$on('$destroy', unbindAddSucceeded);
        $scope.$on('$destroy', unbindEditSucceeded);
    };

    ctrl.refreshPosts = function()
    {
        $http.get('/services/comment/groups/' + ctrl.groupId)
            .then(
                function(httpRestResponse)
                {
                    ctrl.comments = httpRestResponse.data.comments;
                }
            );
    };

    ctrl.currentEdited = {};
    ctrl.currentEdited.controller = null;

    ctrl.initCommentGroupCtrl();
});
