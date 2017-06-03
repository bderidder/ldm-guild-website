(function()
{
    'use strict';

    GetLaDanseApp().directive(
        'qtipTime',
        QTipTime);

    QTipTime.$inject = ['HTMLTemplateService'];

    function QTipTime(HTMLTemplateService)
    {
        return {
            restrict: 'A',
            scope: {
                times: "="
            },
            link: function (scope, element, attrs) {
                var qConfig =
                    {
                        content: {
                            text: function (event, api) {

                                HTMLTemplateService.getCompiledTemplate(
                                    scope,
                                    Assetic.generate('/ladanseangular/ladanse/modules/events/directives/qtipTime/qtipTime.html'),
                                    scope.times
                                ).then(
                                    function (content) {
                                        api.set('content.text', content);
                                    },
                                    function (content) {
                                        api.set('content.text', content);
                                    }
                                );

                                return 'Loading...'; // Set some initial text
                            }
                        },
                        position: {
                            my: "top left",
                            at: "bottom right",
                            target: element
                        },
                        style: 'qtip-bootstrap'
                    };

                jQuery(element).qtip(qConfig);
            }
        };
    }
})();