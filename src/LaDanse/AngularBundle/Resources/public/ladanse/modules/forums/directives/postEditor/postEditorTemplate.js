/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

forumsModule.directive('postEditorTemplate', function() {
    return {
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/forums/directives/postEditor/PostEditor.html'),
    };
});
