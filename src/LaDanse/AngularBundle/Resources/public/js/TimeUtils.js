"use strict";

var TimeUtils = {};

(function()
{
    /*
     *  This method creates a new moment() that is in the given timezone and that takes
     *  the year, month en day from the baseDate, with the given hours and minutes.
     */
    TimeUtils.createMoment = function(baseDate, hours, minutes, timezone)
    {
        /*
         We create a new date based on a format 'YYYY-MM-DD HH:mm' to make
         sure moment.tz() can properly take DST into account.
         */

        // wrap in moment to make sure it is a moment, worst case we made a clone
        var baseMoment = moment(baseDate);

        var formattedDate =
            baseMoment.year().toString()
            + "-"
            + numberToPaddedString(baseMoment.month() + 1)
            + "-"
            + numberToPaddedString(baseMoment.date())
            + " "
            + numberToPaddedString(hours)
            + ":"
            + numberToPaddedString(minutes);

        var newMoment = moment.tz(formattedDate, "YYYY-MM-DD HH:mm", timezone);

        if (!newMoment.isValid())
        {
            throw "TimeUtils.createMoment - Cannot make a valid format from the given parameters";
        }

        return newMoment;
    };

    TimeUtils.createDefaultInviteTime = function(baseDate)
    {
        return TimeUtils.createMoment(baseDate, 19, 15, Constants.REALM_SERVER_TIMEZONE);
    };

    TimeUtils.createDefaultStartTime = function(baseDate)
    {
        return TimeUtils.createMoment(baseDate, 19, 30, Constants.REALM_SERVER_TIMEZONE);
    };

    TimeUtils.createDefaultEndTime = function(baseDate)
    {
        return TimeUtils.createMoment(baseDate, 22, 0, Constants.REALM_SERVER_TIMEZONE);
    };

    /*
     * Return a string representation of the number that is at least 2 long, padded with leading
     * zeroes if needed.
     */
    function numberToPaddedString(number)
    {
        if (number < 10)
        {
            return "0" + number.toString();
        }
        else
        {
            return number.toString();
        }
    }
})();