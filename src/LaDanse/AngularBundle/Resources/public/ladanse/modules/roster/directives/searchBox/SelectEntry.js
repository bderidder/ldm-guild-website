/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

function SelectEntry(value, label)
{
    this.value = value;
    this.label = label;

    this.getValue = function()
    {
        return this.value;
    }

    this.getLabel = function()
    {
        return this.label;
    }
}
