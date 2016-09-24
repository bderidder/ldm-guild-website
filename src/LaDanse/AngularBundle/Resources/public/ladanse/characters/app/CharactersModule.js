/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var CHARACTERS_MODULE_NAME = 'Characters';

var ladanseApp = GetLaDanseApp();

var charactersModule = CreateAngularModule(CHARACTERS_MODULE_NAME);

charactersModule.config(
    ['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider)
        {
            $stateProvider
                .state('characters', {
                    url: '/characters',
                    abstract: true,
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/characters/partials/Characters.html'
                })
                .state('characters.home', {
                    url: '',
                    templateUrl: BUNDLE_BASEPATH + '/ladanseangular/ladanse/characters/partials/CharactersView.html'
                });
        }
    ]
);