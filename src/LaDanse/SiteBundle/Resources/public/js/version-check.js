$( document ).ready(function()
{
    checkSiteVersion();

    window.setInterval(checkSiteVersion, 60 * 1000);
});

function checkSiteVersion()
{
    $.ajax({
        url: Routing.generate('currentVersionAction'),
    })
        .done(function( data )
        {
            var serverDeploymentVersion = data.deploymentVersion;

            if (deploymentVersion != serverDeploymentVersion)
            {
                $("#NewVersionReminderContainer").fadeIn(1500);

                $("#NewVersionReloadButton").click(
                    function()
                    {
                        window.location.reload(true);
                    }
                )
            }
        });
};