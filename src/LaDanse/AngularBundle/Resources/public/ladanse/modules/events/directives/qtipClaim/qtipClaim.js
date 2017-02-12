'use strict';

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive(
    'qtipClaim',
    function (claimTooltipService)
    {
        return {
            restrict: 'A',
            scope: {
                eventId: "=",
                accountId: "="
            },
            link: function (scope, element, attrs)
                {
                    $('div.qtip:visible').qtip('hide');

                    var qConfig =
                    {
                        content: {
                            text: function(event, api)
                            {
                                claimTooltipService.getTooltipHTML(scope, scope.eventId, scope.accountId)
                                    .then(
                                        function(content)
                                        {
                                            api.set('content.text', content);
                                        },
                                        function(content)
                                        {
                                            api.set('content.text', content);
                                        }
                                    );

                                return 'Loading...'; // Set some initial text
                            }
                        },
                        position: {
                            my: "left center",
                            at: "right center",
                            target: element
                        },
                        style: 'qtip-bootstrap',
                        hide: {
                            event: 'unfocus mouseleave',
                            inactive: false,
                            fixed: true,
                            delay: 1000
                        }
                    };

                    jQuery(element).qtip(qConfig);
                }
        };
    });