angular.module('LaDanseApp')

    .directive('teamspeakNav', function()
    {
        return {
            restrict: 'E',
            replace: true,
            controller: 'TeamspeakNavCtrl',
            controllerAs: 'teamspeakNav',
            scope: true,
            templateUrl: '../bundles/ladanseangular/ladanse/directives/teamspeakNav/teamspeakNav.html'
        };
    })

    .controller('TeamspeakNavCtrl', function()
    {
    });
