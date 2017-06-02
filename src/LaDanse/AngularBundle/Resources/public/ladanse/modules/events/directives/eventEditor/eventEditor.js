/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('eventEditor', function()
{
    return {
        restrict: 'E',
        controller: 'EventEditorCtrl',
        controllerAs: 'ctrl',
        scope: {
            'editorModel': '=',
            'callback': '=',
            'simplified': '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/eventEditor/eventEditor.html')
    };
})
.controller('EventEditorCtrl', function($scope)
{
    var ctrl = this;

    ctrl.editorModel = $scope.editorModel;
    ctrl.callback = $scope.callback;
    ctrl.simplified = $scope.simplified;

    ctrl.showExtendedName = !ctrl.simplified;
    ctrl.showExtendedHours = !ctrl.simplified;

    ctrl.formIsValid = false;

    ctrl.masterData = {
        raidInstances: ['The Emerald Nightmare', 'Trial of Valor', 'The Nighthold', 'Tomb of Sargeras'],
        instanceDifficulties: ['To be decided', 'Normal', 'Heroic', 'Mythic']
    };

    ctrl.form = {
        eventType: new Form.RadioButton(),
        hoursType: new Form.RadioButton(),
        eventDate: null,
        raidInstance: '',
        instanceDifficulty: '',
        eventName: '',
        eventDescription: '',
        inviteTime: null,
        startTime: null,
        endTime: null
    };

    ctrl.pickerOptions = {
        autoclose: true,
        //format: 'yyyy-mm-dd',
        //linkFormat: 'DD yyyy-mm-dd',
        language: 'en',
        minView: 'month',
        pickerPosition: 'bottom-left',
        todayBtn: true,
        todayHighlight: true,
        startView: 'month',
        weekStart: 1
    };

    ctrl.updateShowExtended = function()
    {
        ctrl.showCustomName = ctrl.form.eventType.value === 'CustomEvent';
        ctrl.showCustomHours = ctrl.form.hoursType.value === 'CustomHours';
    };

    ctrl.verifyForm = function()
    {
        console.log("START - eventEditor form validation");

        ctrl.formIsValid = true;

        if (ctrl.form.eventType.value === 'CustomEvent')
        {
            if (ctrl.form.eventName === null || ctrl.form.eventName.length <= 1)
            {
                console.log("eventEditor form invalid because event name is empty or too short in custom mode");
                ctrl.formIsValid = false;
            }
        }
        else
        {
            if (ctrl.form.raidInstance === null
                || ctrl.form.raidInstance.length <= 1
                || ctrl.form.instanceDifficulty === null
                || ctrl.form.instanceDifficulty.length <= 1)
            {
                console.log("eventEditor form invalid because raid instance or instance difficulty not given in Legion mode");
                ctrl.formIsValid = false;
            }
        }

        if (ctrl.form.hoursType.value === 'CustomHours')
        {
            if (ctrl.form.inviteTime === null
                || ctrl.form.startTime === null
                || ctrl.form.endTime === null)
            {
                console.log("eventEditor form invalid because invite, start or end time is not supplied in custom hours mode");
                ctrl.formIsValid = false;
            }

            if (ctrl.form.inviteTime > ctrl.form.startTime)
            {
                console.log("eventEditor form invalid because invite is not before or equal to start time in custom hours mode");
                ctrl.formIsValid = false;
            }

            if (ctrl.form.startTime > ctrl.form.endTime)
            {
                console.log("eventEditor form invalid because start is not before or equal to end time in custom hours mode");
                ctrl.formIsValid = false;
            }
        }

        if (ctrl.form.eventDate === null)
        {
            console.log("eventEditor form invalid because event date is not given");
            ctrl.formIsValid = false;
        }

        var now = new Date();

        if (this.getInviteTime() < now)
        {
            console.log("eventEditor form invalid because invite time is before now");
            ctrl.formIsValid = false;
        }

        console.log("END - eventEditor form validation");
    };

    ctrl.saveClicked = function()
    {
        ctrl.verifyForm();
        if (!ctrl.formIsValid) return;

        if (ctrl.form.eventType.value === 'CustomEvent')
        {
            ctrl.editorModel.name = ctrl.form.eventName;
        }
        else
        {
            var eventName = ctrl.form.raidInstance;

            if (ctrl.form.instanceDifficulty !== "To be decided")
            {
                eventName = eventName + " (" + ctrl.form.instanceDifficulty + ")";
            }

            ctrl.editorModel.name = eventName;
        }

        var baseDate = ctrl.form.eventDate;

        if (ctrl.form.hoursType.value === 'CustomHours')
        {
            var inviteTime = TimeUtils.createMoment(
                baseDate,
                ctrl.form.inviteTime.getHours(),
                ctrl.form.inviteTime.getMinutes(),
                Constants.REALM_SERVER_TIMEZONE
            );

            var startTime = TimeUtils.createMoment(
                baseDate,
                ctrl.form.startTime.getHours(),
                ctrl.form.startTime.getMinutes(),
                Constants.REALM_SERVER_TIMEZONE
            );

            var endTime = TimeUtils.createMoment(
                baseDate,
                ctrl.form.endTime.getHours(),
                ctrl.form.endTime.getMinutes(),
                Constants.REALM_SERVER_TIMEZONE
            );

            ctrl.editorModel.inviteTime = inviteTime;
            ctrl.editorModel.startTime = startTime;
            ctrl.editorModel.endTime = endTime;
        }
        else
        {
            ctrl.editorModel.inviteTime = TimeUtils.createDefaultInviteTime(baseDate);
            ctrl.editorModel.startTime = TimeUtils.createDefaultStartTime(baseDate);
            ctrl.editorModel.endTime = TimeUtils.createDefaultEndTime(baseDate);
        }

        ctrl.editorModel.description = ctrl.form.eventDescription;

        ctrl.callback.save();
    };

    ctrl.getInviteTime = function()
    {
        var inviteTime;
        var baseDate = ctrl.form.eventDate;

        if (ctrl.form.hoursType.value === 'CustomHours')
        {
            inviteTime = TimeUtils.createMoment(
                baseDate,
                ctrl.form.inviteTime.getHours(),
                ctrl.form.inviteTime.getMinutes(),
                Constants.REALM_SERVER_TIMEZONE
            ).toDate();
        }
        else
        {
            inviteTime = TimeUtils.createDefaultInviteTime(baseDate).toDate();
        }

        return inviteTime;
    };

    ctrl.initForm = function()
    {
        // if an event name is already given, we force the simplified view
        if (ctrl.editorModel.name !== null && ctrl.editorModel.name.length > 0)
        {
            ctrl.simplified = true;
            ctrl.form.eventName = ctrl.editorModel.name;
        }

        if (ctrl.simplified)
        {
            ctrl.form.eventType.value = 'CustomEvent';
            ctrl.form.hoursType.value = 'CustomHours';
        }
        else
        {
            ctrl.form.eventType.value = 'LegionRaidInstance';
            ctrl.form.hoursType.value = 'NormalHours';
        }

        ctrl.form.eventDescription = ctrl.editorModel.description;
        ctrl.form.eventDate = moment(ctrl.editorModel.inviteTime).tz(Constants.REALM_SERVER_TIMEZONE);
        ctrl.form.inviteTime = moment(ctrl.editorModel.inviteTime).tz(Constants.REALM_SERVER_TIMEZONE).toDate();
        ctrl.form.startTime = moment(ctrl.editorModel.startTime).tz(Constants.REALM_SERVER_TIMEZONE).toDate();
        ctrl.form.endTime = moment(ctrl.editorModel.endTime).tz(Constants.REALM_SERVER_TIMEZONE).toDate();

        $scope.$watch(
            'ctrl.form',
            function (newValue, oldValue, scope)
            {
                ctrl.verifyForm();
            },
            true);

        $scope.$watchCollection(
            '[ctrl.form.eventType.value, ctrl.form.hoursType.value]',
            function (newValue, oldValue, scope)
            {
                ctrl.updateShowExtended();
            },
            true);

        ctrl.updateShowExtended();
    };

    ctrl.initForm();
});
