/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.RaidWeekModel = (function ()
{
    function RaidWeekModel(showDate)
    {
        this._firstDate = null;
        this._lastDate = null;

        this._init(showDate);
    }

    Object.defineProperty(RaidWeekModel.prototype, "firstDate",
        {
            get: function ()
            {
                return this._firstDate.clone();
            }
        }
    );

    Object.defineProperty(RaidWeekModel.prototype, "lastDate",
        {
            get: function ()
            {
                return this._lastDate.clone();
            }
        }
    );

    RaidWeekModel.prototype._init = function(showDate)
    {
        var showMoment = moment(showDate);

        var deltaToStart = 0;
        var deltaToEnd = 0;

        var isoWeekday = showMoment.isoWeekday();

        switch (isoWeekday)
        {
            case 1: // Monday
                deltaToStart = 5;
                deltaToEnd   = 1;
                break;
            case 2: // Tuesday (= end of raid week)
                deltaToStart = 6;
                deltaToEnd   = 0;
                break;
            case 3: // Wednesday (= start of raid week)
                deltaToStart = 0;
                deltaToEnd   = 6;
                break;
            case 4: // Thursday
                deltaToStart = 1;
                deltaToEnd   = 5;
                break;
            case 5: // Friday
                deltaToStart = 2;
                deltaToEnd   = 4;
                break;
            case 6: // Saturday
                deltaToStart = 3;
                deltaToEnd   = 3;
                break;
            case 7: // Sunday
                deltaToStart = 4;
                deltaToEnd   = 2;
                break;
        }

        this._firstDate = showMoment.clone();
        this._firstDate.subtract(deltaToStart, 'day');

        this._lastDate = showMoment.clone();
        this._lastDate.add(deltaToEnd, 'day');
    };

    RaidWeekModel.prototype.isInRaidWeek = function(date)
    {
        var dateMoment = moment(date);

        return (this._firstDate.isSame(dateMoment, 'day')
            || this._lastDate.isSame(dateMoment, 'day')
            || dateMoment.isBetween(this._firstDate, this._lastDate, 'day')
        );
    };

    return RaidWeekModel;
})();