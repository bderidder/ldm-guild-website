/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('signUpEditor', function()
{
    return {
        restrict: 'E',
        controller: 'SignUpEditorCtrl',
        controllerAs: 'ctrl',
        scope: {
            'event': '=',
            'editorModel': '=',
            'callback': '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/signUpEditor/signUpEditor.html')
    };
})
.controller('SignUpEditorCtrl', function($scope, $rootScope)
{
    var ctrl = this;

    ctrl.event = $scope.event;
    ctrl.editorModel = $scope.editorModel;

    ctrl.canSave = true;

    ctrl.form = {
        forTank:    new Form.CheckBox(),
        forHealer:  new Form.CheckBox(),
        forDPS:     new Form.CheckBox(),
        signUpType: new Form.RadioButton()
    };

    ctrl.saveClicked = function()
    {
        if (ctrl.canSave)
        {
            ctrl.editorModel.type = ctrl.form.signUpType.value;

            var roles = [];

            if (ctrl.form.forTank.checked) roles.push("Tank");
            if (ctrl.form.forHealer.checked) roles.push("Healer");
            if (ctrl.form.forDPS.checked) roles.push("DPS");

            ctrl.editorModel.roles = roles;

            $scope.callback.save();
        }
    };

    ctrl.cancelClicked = function()
    {
        $scope.callback.cancel();
    };

    ctrl.resetClicked = function()
    {
        ctrl.initForm();
    };

    ctrl.updateFormWhenAbsence = function()
    {
        if (ctrl.form.signUpType.value == "Absence")
        {
            ctrl.form.forTank.checked = false;
            ctrl.form.forTank.disabled = true;
            ctrl.form.forHealer.checked = false;
            ctrl.form.forHealer.disabled = true;
            ctrl.form.forDPS.checked = false;
            ctrl.form.forDPS.disabled = true;
        }
        else
        {
            ctrl.form.forTank.disabled = false;
            ctrl.form.forHealer.disabled = false;
            ctrl.form.forDPS.disabled = false;
        }
    };

    ctrl.checkIfFormIsValid = function()
    {
        if (ctrl.form.signUpType.value != "Absence")
        {
            ctrl.canSave = !(
                !ctrl.form.forTank.checked
                &&
                !ctrl.form.forHealer.checked
                &&
                !ctrl.form.forDPS.checked);
        }
        else
        {
            ctrl.canSave = true;
        }
    };

    ctrl.initForm = function()
    {
        ctrl.form.forTank.checked = false;
        ctrl.form.forHealer.checked = false;
        ctrl.form.forDPS.checked = false;

        ctrl.form.signUpType.value = ctrl.editorModel.type;

        for(var i = 0; i < ctrl.editorModel.roles.length; i++)
        {
            if (ctrl.editorModel.roles[i] == "Tank")
                ctrl.form.forTank.checked = true;
            if (ctrl.editorModel.roles[i] == "Healer")
                ctrl.form.forHealer.checked = true;
            if (ctrl.editorModel.roles[i] == "DPS")
                ctrl.form.forDPS.checked = true;
        }

        ctrl.updateFormWhenAbsence();
        ctrl.checkIfFormIsValid();

        $scope.$watch(
            'ctrl.form',
            function (newValue, oldValue, scope)
            {
                ctrl.updateFormWhenAbsence();
                ctrl.checkIfFormIsValid();
            },
            true);
    };

    ctrl.initForm();
});
