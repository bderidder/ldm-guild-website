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
            callback: '=',
            searchCriteria: '='
        },
        templateUrl: Assetic.generate('/ladanseangular/ladanse/modules/roster/directives/searchBox/searchBox.html')
    };
})
    .controller('CharacterSearchBoxCtrl', function($scope, $rootScope, gameDataService)
    {
        var ctrl = this;

        ctrl.advancedSearch = false;

        ctrl.initForm = function(searchCriteria)
        {
            ctrl.name = searchCriteria.getName() == null ? "" : searchCriteria.getName();
            ctrl.raider = searchCriteria.getRaider();
            ctrl.claimed = searchCriteria.getClaimed();
            ctrl.claimingMember = searchCriteria.getClaimingMember() == null ? "" : searchCriteria.getClaimingMember();
            ctrl.minLevel = searchCriteria.getMinLevel();
            ctrl.maxLevel = searchCriteria.getMaxLevel();
            ctrl.guild = searchCriteria.getGuild() == null ? null : searchCriteria.getGuild();
            ctrl.race = null;
            ctrl.class = null;
            ctrl.faction = null;
            ctrl.playsTank = false;
            ctrl.playsHealer = false;
            ctrl.playsDPS = false;
        }

        ctrl.resetForm = function()
        {
            ctrl.name = "";
            ctrl.raider = 1;
            ctrl.claimed = 1;
            ctrl.claimingMember = "";
            ctrl.minLevel = 1;
            ctrl.maxLevel = 120;
            ctrl.guild = null;
            ctrl.race = null;
            ctrl.class = null;
            ctrl.faction = null;
            ctrl.playsTank = false;
            ctrl.playsHealer = false;
            ctrl.playsDPS = false;

            ctrl.factionDisabled = false;
            ctrl.claimedDisabled = false;
        };

        ctrl.enterPressed = function()
        {
            ctrl.searchButtonClicked();
        };

        this.raceUpdated = function()
        {
            if (ctrl.race != null)
            {
                ctrl.factionDisabled = true;
                ctrl.faction = null;
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
            ctrl.minLevel = 120;
            ctrl.maxLevel = 120;
        };

        ctrl.toggleSearchScope = function()
        {
            ctrl.advancedSearch = !ctrl.advancedSearch;
        };

        ctrl.searchButtonClicked = function()
        {
            var searchCriteria = new SearchCriteria();

            searchCriteria.setName(ctrl.name);

            if (ctrl.advancedSearch)
            {
                searchCriteria.setMinLevel(ctrl.minLevel);
                searchCriteria.setMaxLevel(ctrl.maxLevel);
                searchCriteria.setGuild(ctrl.guild);
                searchCriteria.setGameClass(ctrl.class);
                searchCriteria.setGameRace(ctrl.race);
                searchCriteria.setGameFaction(ctrl.faction);
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
                searchCriteria.setMaxLevel(120);
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
        };

        gameDataService.getGameData()
            .then(
                function(gameDataModel)
                {
                    console.log("searchBox - got game data");

                    ctrl.gameDataModel = gameDataModel;

                    var i;
                    var selectEntry = null;

                    var guilds = ctrl.gameDataModel.getGuilds();
                    var guildEntries = [];
                    for (i = 0; i < guilds.length; i++)
                    {
                        var guild = guilds[i];

                        selectEntry = new SelectEntry(
                            guild.id,
                            guild.name,
                            ctrl.gameDataModel.getRealm(guild.realmReference.id).name
                        );

                        guildEntries.push(selectEntry);
                    }
                    ctrl.guildEntries = guildEntries;

                    var gameRaces = ctrl.gameDataModel.getGameRaces();
                    var gameRaceEntries = [];
                    for (i = 0; i < gameRaces.length; i++)
                    {
                        var gameRace = gameRaces[i];

                        selectEntry = new SelectEntry(
                            gameRace.id,
                            gameRace.name,
                            ctrl.gameDataModel.getGameFaction(gameRace.gameFactionReference.id).name
                        );

                        gameRaceEntries.push(selectEntry);
                    }
                    ctrl.gameRaceEntries = gameRaceEntries;

                    var gameClasses = ctrl.gameDataModel.getGameClasses();
                    var gameClassEntries = [];
                    for (i = 0; i < gameClasses.length; i++)
                    {
                        var gameClass = gameClasses[i];

                        selectEntry = new SelectEntry(
                            gameClass.id,
                            gameClass.name
                        );

                        gameClassEntries.push(selectEntry);
                    }
                    ctrl.gameClassEntries = gameClassEntries;

                    var gameFactions = ctrl.gameDataModel.getGameFactions();
                    var gameFactionEntries = [];
                    for (i = 0; i < gameFactions.length; i++)
                    {
                        var gameFaction = gameFactions[i];

                        selectEntry = new SelectEntry(
                            gameFaction.id,
                            gameFaction.name
                        );

                        gameFactionEntries.push(selectEntry);
                    }
                    ctrl.gameFactionEntries = gameFactionEntries;
                }
            );

        ctrl.initForm($scope.searchCriteria);
    });
