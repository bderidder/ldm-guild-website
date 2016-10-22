/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var rosterModule = GetAngularModule(ROSTER_MODULE_NAME)

rosterModule.directive('characterSearchBox', function()
{
    return {
        restrict: 'E',
        controller: 'CharacterSearchBoxCtrl',
        controllerAs: 'ctrl',
        scope: {
            visible: '=',
            callback: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/directives/searchBox/searchBox.html')
    };
})
    .controller('CharacterSearchBoxCtrl', function($scope, $rootScope, gameDataService)
    {
        var ctrl = this;

        ctrl.advancedSearch = false;

        ctrl.resetForm = function()
        {
            ctrl.name = "";
            ctrl.raider = 1;
            ctrl.claimed = 1;
            ctrl.claimingMember = "";
            ctrl.minLevel = 1;
            ctrl.maxLevel = 110;
            ctrl.guild = "AnyGuild";
            ctrl.race = "AnyRace";
            ctrl.class = "AnyClass";
            ctrl.faction = "AnyFaction";
            ctrl.playsTank = false;
            ctrl.playsHealer = false;
            ctrl.playsDPS = false;

            ctrl.factionDisabled = false;
            ctrl.claimedDisabled = false;
        };

        ctrl.enterPressed = function()
        {
            ctrl.searchButtonClicked();
        }

        this.raceUpdated = function()
        {
            if (ctrl.race != "AnyRace")
            {
                ctrl.factionDisabled = true;
                ctrl.faction = "AnyFaction";
            }
            else
            {
                ctrl.factionDisabled = false;
            }
        };
        $scope.$watch(function () {
            return ctrl.race;
        },ctrl.raceUpdated);

        this.verifyClaimEnabled = function()
        {
            if (
                (ctrl.raider != 1)
                ||
                (ctrl.playsTank || ctrl.playsHealer || ctrl.playsDPS)
                ||
                (ctrl.claimingMember != null && ctrl.claimingMember.length > 0)
            )
            {
                ctrl.claimedDisabled = true;
                ctrl.claimed = 2;
            }
            else
            {
                ctrl.claimedDisabled = false;
            }
        };

        $scope.$watch(function () {
            return ctrl.raider;
        },ctrl.verifyClaimEnabled);

        $scope.$watch(function () {
            return ctrl.playsTank;
        },ctrl.verifyClaimEnabled);
        $scope.$watch(function () {
            return ctrl.playsHealer;
        },ctrl.verifyClaimEnabled);
        $scope.$watch(function () {
            return ctrl.playsDPS;
        },ctrl.verifyClaimEnabled);

        $scope.$watch(function () {
            return ctrl.claimingMember;
        },ctrl.verifyClaimEnabled);

        ctrl.levelCap = function()
        {
            ctrl.minLevel = 110;
            ctrl.maxLevel = 110;
        };

        ctrl.toggleSearchScope = function()
        {
            console.log("Class - " + ctrl.class);

            ctrl.advancedSearch = !ctrl.advancedSearch;
        }

        ctrl.searchButtonClicked = function()
        {
            var searchCriteria = new SearchCriteria();

            searchCriteria.setName(ctrl.name);

            if (ctrl.advancedSearch)
            {
                searchCriteria.setMinLevel(ctrl.minLevel);
                searchCriteria.setMaxLevel(ctrl.maxLevel);
                searchCriteria.setGuild(ctrl.guild !== "AnyGuild" ? ctrl.guild : null);
                searchCriteria.setGameClass(ctrl.class !== "AnyClass" ? ctrl.class : null);
                searchCriteria.setGameRace(ctrl.race !== "AnyRace" ? ctrl.race : null);
                searchCriteria.setGameFaction(ctrl.faction !== "AnyFaction" ? ctrl.faction : null);
                searchCriteria.setRaider(ctrl.raider);
                searchCriteria.setClaimed(ctrl.claimed);

                if (ctrl.playsTank || ctrl.playsHealer || ctrl.playsDPS)
                {
                    var roles = [];

                    ctrl.playsTank ? roles.push("Tank") : null;
                    ctrl.playsHealer ? roles.push("Healer") : null;
                    ctrl.playsDPS ? roles.push("DPS") : null;

                    searchCriteria.setRoles(roles);
                }
                else
                {
                    searchCriteria.setRoles(null);
                }

                if (ctrl.claimingMember != null && ctrl.claimingMember.length > 0)
                {
                    searchCriteria.setClaimingMember(ctrl.claimingMember);
                }
                else
                {
                    searchCriteria.setClaimingMember(null);
                }
            }
            else
            {
                searchCriteria.setMinLevel(1);
                searchCriteria.setMaxLevel(110);
                searchCriteria.setGuild(null);
                searchCriteria.setGameClass(null);
                searchCriteria.setGameRace(null);
                searchCriteria.setGameFaction(null);
                searchCriteria.setRaider(1);
                searchCriteria.setClaimed(1);
                searchCriteria.setRoles(null);
                searchCriteria.setClaimingMember(null);
            }

            $scope.callback(searchCriteria);
        }

        gameDataService.getGameData()
            .then(function(gameDataModel)
            {
                ctrl.gameDataModel = gameDataModel;

                var guilds = ctrl.gameDataModel.getGuilds();
                var guildEntries = [];
                for (var i = 0; i < guilds.length; i++)
                {
                    var guild = guilds[i];

                    var selectEntry = new SelectEntry(
                        guild.id,
                        guild.name + " (" + ctrl.gameDataModel.getRealm(guild.realmReference.id).name + ")"
                    );

                    guildEntries.push(selectEntry);
                }
                ctrl.guildEntries = guildEntries;

                var gameRaces = ctrl.gameDataModel.getGameRaces();
                var gameRaceEntries = [];
                for (var i = 0; i < gameRaces.length; i++)
                {
                    var gameRace = gameRaces[i];

                    var selectEntry = new SelectEntry(
                        gameRace.id,
                        gameRace.name
                    );

                    gameRaceEntries.push(selectEntry);
                }
                ctrl.gameRaceEntries = gameRaceEntries;

                var gameClasses = ctrl.gameDataModel.getGameClasses();
                var gameClassEntries = [];
                for (var i = 0; i < gameClasses.length; i++)
                {
                    var gameClass = gameClasses[i];

                    var selectEntry = new SelectEntry(
                        gameClass.id,
                        gameClass.name
                    );

                    gameClassEntries.push(selectEntry);
                }
                ctrl.gameClassEntries = gameClassEntries;

                var gameFactions = ctrl.gameDataModel.getGameFactions();
                var gameFactionEntries = [];
                for (var i = 0; i < gameFactions.length; i++)
                {
                    var gameFaction = gameFactions[i];

                    var selectEntry = new SelectEntry(
                        gameFaction.id,
                        gameFaction.name
                    );

                    gameFactionEntries.push(selectEntry);
                }
                ctrl.gameFactionEntries = gameFactionEntries;
            });

        ctrl.resetForm();
    });
