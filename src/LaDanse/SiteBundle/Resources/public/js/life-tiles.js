$( document ).ready(function()
{
    var tiles = $(".tile");

    for (var i = 0; i < tiles.length; i++)
    {
        animateTile($(tiles[i]));
    }
});

function animateTile(tile)
{
    tile.find(".tile-content").wrap("<div class='tile-content-wrapper'></div>");

    tile.find(".tile-content").show();
    tile.find(".tile-content-wrapper").eq(0).show();

    var contentDivs = tile.find(".tile-content-wrapper");

    var animationParams =
    {
        delay:     fetchDataValue(tile, "delay", 5000),
        speed:     fetchDataValue(tile, "speed", 700),
        direction: fetchDataValue(tile, "direction", "horizontal"),
    };
    
    if (contentDivs.length <= 1)
        return;

    var currentVisible = 0;

    setTimeout(function()
        {
            slideInSlideOut(contentDivs, currentVisible, animationParams)
        }, animationParams.delay);
}

function slideInSlideOut(divArray, currentVisible, animationParams)
{
    var nextVisible = currentVisible + 1;

    if (nextVisible >= divArray.length)
        nextVisible = 0;

    var currentDiv = divArray[currentVisible];
    var nextDiv = divArray[nextVisible];

    switch(animationParams.direction)
    {
        case "horizontal":
            hideDirection = "left";
            showDirection = "right";
            break;
        case "vertical":
            hideDirection = "up";
            showDirection = "down";
            break;    
        default:
            hideDirection = "left";
            showDirection = "right";
            break;
    }

    $(currentDiv).hide('slide', { direction: hideDirection }, animationParams.speed);
    $(nextDiv).show('slide', { direction: showDirection }, animationParams.speed);

    setTimeout(function()
        {
            slideInSlideOut(divArray, nextVisible, animationParams)
        }, animationParams.delay);
}

function fetchDataValue(element, name, defaultValue)
{
    value = element.data(name);

    if ((typeof value == "undefined") || (value == null))
    {
        value = defaultValue;
    }

    return value;
}