/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.MonthModel = (function ()
{
    function MonthModel(showDate, raidWeekModel)
    {
        this._firstDate = this._calculateFirstDay(showDate, raidWeekModel);
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

    MonthModel.prototype.populateEvents = function(events)
    {
        for (var i = 0; i < events.length; i++)
        {
            var currentEvent = events[i];

            for(var j = 0; j < this._weeks.length; j++)
            {
                var currentWeek = this._weeks[j];

                if (currentWeek.isInWeek(currentEvent.inviteTime))
                {
                    currentWeek.addEvent(currentEvent);
                }
            }
        }
    };

    MonthModel.prototype._calculateFirstDay = function(showDate, raidWeekModel)
    {
        var firstDay = moment(showDate).clone().isoWeekday(1);

        if (raidWeekModel.isInRaidWeek(firstDay))
        {
            // a monday and it fits in the current raid week, we have to move 7 days back

            return firstDay.subtract(7, 'day');
        }

        var lastDay = firstDay.clone().add(28, 'day');

        /* we comment this out as not showing the entire raid week at the end of the months is not really an issue
           and it fixes a "back button" issue
        if (raidWeekModel.isInRaidWeek(lastDay))
        {
            // a friday and it fits in the current raid week, we have to move 7 days forward

            return firstDay.add(7, 'day');
        }
         */

        return firstDay;
    };

    return MonthModel;
})();