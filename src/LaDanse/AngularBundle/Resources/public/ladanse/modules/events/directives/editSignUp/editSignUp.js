/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('editSignUp', function()
{
    return {
        restrict: 'E',
        controller: 'EditSignUpCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/editSignUp/editSignUp.html')
    };
})
.controller('EditSignUpCtrl', function($scope, $rootScope, $stateParams, $state, eventService)
{
    var ctrl = this;

    ctrl.eventId = $stateParams.eventId;
    ctrl.signUpId = $stateParams.signUpId;

    ctrl.event = null;
    ctrl.signUp = null;
    ctrl.editorModel = null;

    ctrl.callback = {
        save: function(signUpDto)
        {
            ctrl.saveSignUp();
        },
        cancel: function()
        {
            console.log("cancelClicked");

            console.log(ctrl.editorModel);
        }
    };

    ctrl.setupEventAndSignUp = function(eventDto)
    {
        ctrl.event = eventDto;

        for(var i = 0; i < ctrl.event.signUps.length; i++)
        {
            if (ctrl.event.signUps[i].id == ctrl.signUpId)
            {
                ctrl.signUp = ctrl.event.signUps[i];
                ctrl.editorModel = new SignUpEditorModel();

                ctrl.editorModel.type = ctrl.event.signUps[i].type;
                ctrl.editorModel.roles = ctrl.event.signUps[i].roles.slice(0);
            }
        }
    };

    ctrl.saveSignUp = function()
    {
        var putSignUp = new DTO.Events.PutSignUp();

        putSignUp.type = ctrl.editorModel.type;
        putSignUp.roles = ctrl.editorModel.roles;

        var idReference = new DTO.Shared.IdReference();
        idReference.id = ctrl.signUp.accountRef.id;

        putSignUp.accountRef = idReference;

        eventService.putSignUp(ctrl.eventId, ctrl.signUpId, putSignUp)
            .then(
                function(httpRestResponse)
                {
                    alertify.success('Updated sign up');

                    $state.go('events.event.view', { eventId: ctrl.eventId });
                },
                function(httpRestResponse)
                {
                    alertify.error('Failed to update sign up');
                }
            );
    };

    ctrl.init = function()
    {
        var promise = eventService.getEventById(ctrl.eventId);

        promise.then(
            function(eventDto)
            {
                try
                {
                    ctrl.setupEventAndSignUp(eventDto)
                }
                catch(e)
                {
                    console.log(e);
                }
            },
            function(reason)
            {
                console.log('Failed to get event - ' + reason);
            }
        );
    };

    ctrl.init();
});
