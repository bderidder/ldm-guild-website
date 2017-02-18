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
.controller('EventEditorCtrl', function($scope, $rootScope)
{
    var ctrl = this;

    ctrl.editorModel = $scope.editorModel;
    ctrl.callback = $scope.callback;
    ctrl.simplified = $scope.simplified;

    ctrl.showExtendedName = !ctrl.simplified;
    ctrl.showExtendedHours = !ctrl.simplified;

    ctrl.formIsValid = false;

    ctrl.masterData = {
        raidInstances: ['The Emerald Nightmare', 'Trial of Valor', 'Nighthold'],
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
        ctrl.showCustomName = ctrl.form.eventType.value == 'CustomEvent';
        ctrl.showCustomHours = ctrl.form.hoursType.value == 'CustomHours';
    };

    ctrl.verifyForm = function()
    {
        ctrl.formIsValid = true;

        if (ctrl.form.eventType.value == 'CustomEvent')
        {
            ctrl.formIsValid &= (ctrl.form.eventName != null && ctrl.form.eventName.length > 1)
        }
        else
        {
            ctrl.formIsValid &=
                (ctrl.form.raidInstance != null && ctrl.form.raidInstance.length > 1)
                &&
                (ctrl.form.instanceDifficulty != null && ctrl.form.instanceDifficulty.length > 1);
        }

        if (ctrl.form.hoursType.value == 'CustomHours')
        {
            ctrl.formIsValid &=
                ctrl.form.inviteTime != null
                &&
                ctrl.form.startTime != null
                &&
                ctrl.form.endTime != null;

            ctrl.formIsValid &= ctrl.form.inviteTime <= ctrl.form.startTime;
            ctrl.formIsValid &= ctrl.form.startTime <= ctrl.form.endTime;
        }

        var now = new Date();

        ctrl.formIsValid &= ctrl.form.eventDate != null;

        ctrl.formIsValid &= this.getInviteTime() >= now;
    };

    ctrl.saveClicked = function()
    {
        ctrl.verifyForm();
        if (!ctrl.formIsValid) return;

        if (ctrl.form.eventType.value == 'CustomEvent')
        {
            ctrl.editorModel.name = ctrl.form.eventName;
        }
        else
        {
            var eventName = ctrl.form.raidInstance;

            if (ctrl.form.instanceDifficulty != "To be decided")
            {
                eventName = eventName + " (" + ctrl.form.instanceDifficulty + ")";
            }

            ctrl.editorModel.name = eventName;
        }

        var inviteTime;
        var startTime;
        var endTime;

        var baseDate = ctrl.form.eventDate;

        if (ctrl.form.hoursType.value == 'CustomHours')
        {
            inviteTime = new Date(baseDate.valueOf());
            inviteTime.setHours(ctrl.form.inviteTime.getHours());
            inviteTime.setMinutes(ctrl.form.inviteTime.getMinutes());

            startTime = new Date(baseDate.valueOf());
            startTime.setHours(ctrl.form.startTime.getHours());
            startTime.setMinutes(ctrl.form.startTime.getMinutes());

            endTime = new Date(baseDate.valueOf());
            endTime.setHours(ctrl.form.endTime.getHours());
            endTime.setMinutes(ctrl.form.endTime.getMinutes());

            ctrl.editorModel.inviteTime = inviteTime;
            ctrl.editorModel.startTime = startTime;
            ctrl.editorModel.endTime = endTime;
        }
        else
        {
            inviteTime = new Date(baseDate.valueOf());
            inviteTime.setHours(19);
            inviteTime.setMinutes(15);

            startTime = new Date(baseDate.valueOf());
            startTime.setHours(19);
            startTime.setMinutes(30);

            endTime = new Date(baseDate.valueOf());
            endTime.setHours(22);
            endTime.setMinutes(0);

            ctrl.editorModel.inviteTime = inviteTime;
            ctrl.editorModel.startTime = startTime;
            ctrl.editorModel.endTime = endTime;
        }

        ctrl.editorModel.description = ctrl.form.eventDescription;

        ctrl.callback.save();
    };

    ctrl.getInviteTime = function()
    {
        var inviteTime;
        var baseDate = ctrl.form.eventDate;

        if (ctrl.form.hoursType.value == 'CustomHours')
        {
            inviteTime = new Date(baseDate.valueOf());
            inviteTime.setHours(ctrl.form.inviteTime.getHours());
            inviteTime.setMinutes(ctrl.form.inviteTime.getMinutes());
        }
        else
        {
            inviteTime = new Date(baseDate.valueOf());
            inviteTime.setHours(19);
            inviteTime.setMinutes(15);
        }

        return inviteTime;
    };

    ctrl.initForm = function()
    {
        // if an event name is already given, we force the simplified view
        if (ctrl.editorModel.name != null && ctrl.editorModel.name.length > 0)
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
        ctrl.form.eventDate = new Date(ctrl.editorModel.inviteTime.valueOf());
        ctrl.form.inviteTime = new Date(ctrl.editorModel.inviteTime.valueOf());
        ctrl.form.startTime = new Date(ctrl.editorModel.startTime.valueOf());
        ctrl.form.endTime = new Date(ctrl.editorModel.endTime.valueOf());

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
