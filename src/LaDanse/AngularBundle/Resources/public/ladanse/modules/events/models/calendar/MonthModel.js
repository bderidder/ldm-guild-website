/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.MonthModel = (function ()
{
    function MonthModel(showDate, raidWeekModel)
    {
        this._firstDate = this._calculateFirstDay(showDate);
        this._raidWeekModel = raidWeekModel;
        this._weeks = [];

        var firstWeekDate = this._firstDate.clone();
        for(var i = 0; i < 4; i++)
        {
            var weekModel = new Calendar.WeekModel(firstWeekDate, this._raidWeekModel);

            this._weeks.push(weekModel);

            firstWeekDate.add(7, 'day');
        }

        this._weeks[0].days[0].showMonth = true;
    }

    Object.defineProperty(MonthModel.prototype, "firstDate",
        {
            get: function ()
            {
                return this._firstDate;
            }
        }
    );

    Object.defineProperty(MonthModel.prototype, "weeks",
        {
            get: function ()
            {
                return this._weeks;
            }
        }
    );

    MonthModel.prototype._calculateFirstDay = function(showDate)
    {
        return moment(showDate).clone().isoWeekday(1);
    };

    return MonthModel;
})();