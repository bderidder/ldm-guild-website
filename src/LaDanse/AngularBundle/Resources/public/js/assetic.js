/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var Assetic = (function ()
{
    var my = {};

    my.generate = function (assetBundlePath)
    {
        return BUNDLE_BASEPATH + assetBundlePath + "?" + deploymentVersion;
    };

    return my;
}());
