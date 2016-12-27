'use strict';

var eventsModule = GetAngularModule(EVENTS_MODULE_NAME);

eventsModule.directive(
    'qtipClaim',
    function (eventService)
    {
        return {
            restrict: 'A',
            scope: {
                eventId: "=",
                accountId: "="
            },
            link: function (scope, element, attrs)
                {
                    var qonfig =
                    {
                        content: {
                            text: function(event, api)
                            {
                                eventService.listClaims(scope.eventId, scope.accountId)
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
                            event: 'unfocus',
                            inactive: false
                        }
                    };

                    jQuery(element).qtip(qonfig);
                }
        };
    });