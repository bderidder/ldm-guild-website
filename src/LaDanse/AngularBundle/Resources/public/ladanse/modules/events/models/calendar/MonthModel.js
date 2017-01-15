/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

Calendar.MonthModel = (function ()
{
    function MonthModel(showDate)
    {
        this._date = date;
        this._events = [];
        this._showMonth = false;
    }

    Object.defineProperty(MonthModel.prototype, "date",
        {
            get: function ()
            {
                return this._date;
            }
        }
    );

    Object.defineProperty(MonthModel.prototype, "events",
        {
            get: function ()
            {
                return this._events;
            }
        }
    );

    Object.defineProperty(MonthModel.prototype, "showMonth",
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

    Object.defineProperty(MonthModel.prototype, "display",
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

    MonthModel.prototype.addEvent = function(event)
    {
        this._events.push(event);
    }

    return MonthModel;
})();