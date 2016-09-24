/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumsModule.directive('forumItemTemplate', function() {
    return {
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/directives/forumItem/ForumItemView.html'),
    };
});
