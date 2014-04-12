$( document ).ready(function()
{
    var tile = $("#tile");
    var contentDivs = tile.find(".tile-content");

    var contentDelay = 5000;
    var slideSpeed = 700;

    if (contentDivs.length <= 1)
        return;

    var currentVisible = 0;

    setTimeout(function()
        {
            slideInSlideOut(contentDivs, currentVisible, contentDelay, slideSpeed)
        }, contentDelay);
});

function slideInSlideOut(divArray, currentVisible, contentDelay, slideSpeed)
{
    var nextVisible = currentVisible + 1;

    if (nextVisible >= divArray.length)
        nextVisible = 0;

    var currentDiv = divArray[currentVisible];
    var nextDiv = divArray[nextVisible];

    $(currentDiv).hide('slide', { direction: 'left' }, slideSpeed);
    $(nextDiv).show('slide', { direction: 'right' }, slideSpeed);

    setTimeout(function()
        {
            slideInSlideOut(divArray, nextVisible, contentDelay, slideSpeed)
        }, contentDelay);
}