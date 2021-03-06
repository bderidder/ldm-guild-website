function tsClientsSuccessFunc(data, textStatus, jqXHR)
{
    var clientsList = data;

    $('#teamspeakTile').empty();

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

    $('#teamspeakTile').append("<div class=\"label\"><i class=\"fa fa-headphones fa-2x\"></i></div>");

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

    $('#teamspeakTile').empty();

    var errorString = '';

    errorString += "<div>";
    errorString += "  <div class='ts-top-label'>";
    errorString += "     <p style='font-size: 0.85em; text-align: center;'>We are sorry but there<br/>was a problem fetching<br/>the list of people online</p>";
    errorString += "  </div>";
    errorString += "</div>";

    //appending to the div
    $('#teamspeakTile').append($(errorString));

    $('#teamspeakTile').append("<div class=\"label\"><i class=\"fa fa-headphones fa-2x\"></i></div>");

    $('#teamspeakTile').removeClass('tile').addClass('tile');
}