/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.DayModel = (function ()
{
    function DayModel(date)
    {
        this._date = date;
        this._events = [];
        this._showMonth = false;
        this._isInCurrentRaidWeek = false;
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

    Object.defineProperty(DayModel.prototype, "isInCurrentRaidWeek",
        {
            get: function ()
            {
                return this._isInCurrentRaidWeek;
            },
            set: function (isInCurrentRaidWeek)
            {
                this._isInCurrentRaidWeek = isInCurrentRaidWeek;
            },
            enumerable: true
        }
    );

    Object.defineProperty(DayModel.prototype, "display",
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