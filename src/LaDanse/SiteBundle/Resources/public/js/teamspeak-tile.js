function tsClientsSuccessFunc(data, textStatus, jqXHR)
{
    var clientsList = data;

    // creating html string
    var htmlString = "";

    if (clientsList.length === 0)
    {
        htmlString += "<div>";
        htmlString += "  <div class='ts-top-label'>";
        htmlString += "     <p style='font-size: 0.85em; text-align: center;'>There is nobody<br/> online now on TS</p>";
        htmlString += "  </div>";
        htmlString += "</div>";

        $('#teamspeakTile').append($(htmlString));
    }
    else
    {
        htmlString += "<div>";
        htmlString += "  <div class='ts-top-label'>";
        htmlString += "     <p style='font-size: 0.85em; text-align: center;'>Come online now<br/> and talk to</p>";
        htmlString += "  </div>";
        htmlString += "</div>";

        $('#teamspeakTile').append($(htmlString));

        for (var j = 0; j < clientsList.length; j++)
        {
            var client = clientsList[j];
        
            htmlString = "";

            htmlString += "<div class='tile-content'>";
            htmlString += "  <div style='padding: 15px; padding-top: 55px;'>";
            htmlString += "     <p style='font-size: 1.4em; text-align: center;'>"
                + sanitizeClientNickname(client.client_nickname) + "</p> ";
            htmlString += "  </div>";
            htmlString += "</div>";

            //appending to the div
            $('#teamspeakTile').append($(htmlString));
        }
    }

    $('#teamspeakTile').removeClass('tile').addClass('tile');

    animateTile($('#teamspeakTile'));
}

function sanitizeClientNickname(nickname)
{
    var maxLength = 10;

    if (nickname.length > maxLength)
    {
        return nickname.substring(0, maxLength - 1) + "...";
    }

    return nickname;
}

function tsClientsErrorFunc(jqXHR, textStatus, errorThrown)
{
    console.log("Error fetching online TS people " + textStatus);
    console.log(errorThrown);

    var errorString = '';

    errorString += "<div>";
    errorString += "  <div class='ts-top-label'>";
    errorString += "     <p style='font-size: 0.85em; text-align: center;'>We are sorry but there was a problem fetching the list of online people</p>";
    errorString += "  </div>";
    errorString += "</div>";

    //appending to the div
    $('#teamspeakTile').append($(errorString));
}