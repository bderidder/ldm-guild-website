angular.module('LaDanseApp')

    .directive('mainMenu', function()
    {
        return {
            restrict: 'E',
            replace: true,
            controller: 'MainMenuCtrl',
            controllerAs: 'mainMenu',
            scope: true,
            templateUrl: '../bundles/ladanseangular/ladanse/directives/mainMenu/mainMenu.html'
        };
    })

    .controller('MainMenuCtrl', function()
    {
    });
