/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var EventModel = (function ()
{
    function EventModel(eventDto)
    {
        this._eventDto = eventDto;

        this._init();
    }

    Object.defineProperty(EventModel.prototype, "id",
        {
            get: function ()
            {
                return this._eventDto.id;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "name",
        {
            get: function ()
            {
                return this._eventDto.name;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "description",
        {
            get: function ()
            {
                return this._eventDto.description;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "organiserRef",
        {
            get: function ()
            {
                return this._eventDto.organiserRef;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "inviteTime",
        {
            get: function ()
            {
                return this._eventDto.inviteTime;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "startTime",
        {
            get: function ()
            {
                return this._eventDto.startTime;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "endTime",
        {
            get: function ()
            {
                return this._eventDto.endTime;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "state",
        {
            get: function ()
            {
                return this._eventDto.state;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "commentGroupRef",
        {
            get: function ()
            {
                return this._eventDto.commentGroupRef;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "willComeSignUps",
        {
            get: function ()
            {
                return this._willComeSignUps;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "mightComeSignUps",
        {
            get: function ()
            {
                return this._mightComeSignUps;
            }
        }
    );

    Object.defineProperty(EventModel.prototype, "absenceSignUps",
        {
            get: function ()
            {
                return this._absenceSignUps;
            }
        }
    );

    EventModel.prototype._init = function()
    {
        this.currentUserSignedUp = false;
        this.currentUserIsOrganiser = (this.organiserRef.id == currentAccount.id);

        var now = new Date();

        this.isFuture = (this.inviteTime.getTime() > now.getTime());

        this.isPending = (this.state == 'Pending');
        this.isCancelled = (this.state == 'Cancelled');
        this.isConfirmed = (this.state == 'Confirmed');

        this._willComeSignUps = [];
        this._mightComeSignUps = [];
        this._absenceSignUps = [];

        this.willComeCount = 0;
        this.mightComeCount = 0;
        this.totalAbsence = 0;

        this.willComeTankCount = 0;
        this.willComeHealerCount = 0;
        this.willComeDPSCount = 0;

        this.mightComeTankCount = 0;
        this.mightComeHealerCount = 0;
        this.mightComeDPSCount = 0;

        var signUpCount = this._eventDto.signUps.length;
        for (var i = 0; i < signUpCount; i++)
        {
            var signUpModel = new SignUpModel(this._eventDto.signUps[i]);

            this.currentUserSignedUp = this.currentUserSignedUp || signUpModel.isForCurrentUser;

            if (signUpModel.isWillCome)
            {
                this.willComeCount++;

                if (signUpModel.isForTank) this.willComeTankCount++;
                if (signUpModel.isForHealer) this.willComeHealerCount++;
                if (signUpModel.isForDPS) this.willComeDPSCount++;

                this._willComeSignUps.push(signUpModel);
            }

            if (signUpModel.isMightCome)
            {
                this.mightComeCount++;

                if (signUpModel.isForTank) this.mightComeTankCount++;
                if (signUpModel.isForHealer) this.mightComeHealerCount++;
                if (signUpModel.isForDPS) this.mightComeDPSCount++;

                this._mightComeSignUps.push(signUpModel);
            }

            if (signUpModel.isAbsence)
            {
                this.totalAbsence++;

                this._absenceSignUps.push(signUpModel);
            }
        }

        this.signUpCount = this.willComeCount + this.mightComeCount + this.totalAbsence;
    }

    return EventModel;
})();