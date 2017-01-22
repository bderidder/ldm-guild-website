/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.WeekModel = (function ()
{
    function WeekModel(firstDate, raidWeekModel)
    {
        this._raidWeekModel = raidWeekModel;
        this._firstDate = moment(firstDate).clone();
        this._days = [];

        for(var firstDateDelta = 0; firstDateDelta < 7; firstDateDelta++)
        {
            var weekStart = this._firstDate.clone();
            weekStart.add(firstDateDelta, 'day');

            var dayModel = new Calendar.DayModel(weekStart, this._raidWeekModel);

            dayModel.showMonth = (weekStart.date() == 1);

            this._days.push(dayModel);
        }
    }

    Object.defineProperty(WeekModel.prototype, "firstDay",
        {
            get: function ()
            {
                return this._firstDate;
            }
        }
    );

    Object.defineProperty(WeekModel.prototype, "days",
        {
            get: function ()
            {
                return this._days;
            }
        }
    );

    return WeekModel;
})();