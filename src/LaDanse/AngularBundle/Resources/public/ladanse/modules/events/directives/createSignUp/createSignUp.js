/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

"use strict";

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive('createSignUp', function()
{
    return {
        restrict: 'E',
        controller: 'CreateSignUpCtrl',
        controllerAs: 'ctrl',
        scope: {},
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/events/directives/createSignUp/createSignUp.html')
    };
})
.controller('CreateSignUpCtrl', function($scope, $rootScope, $stateParams, $state, eventService)
{
    var ctrl = this;

    ctrl.eventId = $stateParams.eventId;

    ctrl.event = null;
    ctrl.editorModel = null;

    ctrl.callback = {
        save: function()
        {
            ctrl.saveSignUp();
        },
        cancel: function()
        {
            $state.go('events.event.view', { eventId: ctrl.eventId });
        }
    };

    ctrl.setupEventAndSignUp = function(eventDto)
    {
        ctrl.event = eventDto;

        ctrl.editorModel = new SignUpEditorModel();
        ctrl.editorModel.type = "WillCome";
        ctrl.editorModel.roles = [];
    };

    ctrl.saveSignUp = function()
    {
        var postSignUp = new DTO.Events.PostSignUp();

        postSignUp.type = ctrl.editorModel.type;
        postSignUp.roles = ctrl.editorModel.roles;

        var idReference = new DTO.Shared.IdReference();
        idReference.id = currentAccount.id;

        postSignUp.accountRef = idReference;

        eventService.postSignUp(ctrl.eventId, postSignUp)
            .then(
                function(eventDto)
                {
                    alertify.success('Created sign up');

                    $state.go('events.event.view', { eventId: ctrl.eventId });
                },
                function(errorMessage)
                {
                    alertify.error('Failed to create sign up');
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
