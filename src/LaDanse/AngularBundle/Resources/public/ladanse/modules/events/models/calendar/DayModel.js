/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.DayModel = (function ()
{
    function DayModel(date, raidWeekModel)
    {
        this._date = moment(date);
        this._raidWeekModel = raidWeekModel;
        this._events = [];
        this._showMonth = false;
    }

    Object.defineProperty(DayModel.prototype, "date",
        {
            get: function ()
            {
                return this._date;
            }
        }
    );

    Object.defineProperty(DayModel.prototype, "events",
        {
            get: function ()
            {
                return this._events;
            }
        }
    );

    Object.defineProperty(DayModel.prototype, "inThePast",
        {
            get: function ()
            {
                return this._date.isBefore(moment(), 'day');
            }
        }
    );

    Object.defineProperty(DayModel.prototype, "showMonth",
        {
            get: function ()
            {
                return this._showMonth;
            },
            set: function (showMonth)
            {
                this._showMonth = showMonth;
            },
            enumerable: true
        }
    );

    Object.defineProperty(DayModel.prototype, "inRaidWeek",
        {
            get: function ()
            {
                return this._raidWeekModel.isInRaidWeek(this._date);
            }
        }
    );

    Object.defineProperty(DayModel.prototype, "isToday",
        {
            get: function ()
            {
                return this._date.isSame(moment(), 'day');
            }
        }
    );

    Object.defineProperty(DayModel.prototype, "displayString",
        {
            get: function ()
            {
                if (this.showMonth)
                {
                    return moment(this.date).format('MMM DD');
                }
                else
                {
                    return moment(this.date).format('DD');
                }
            }
        }
    );

    DayModel.prototype.addEvent = function(event)
    {
        this._events.push(event);
    };

    return DayModel;
})();