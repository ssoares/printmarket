var zIndexNumber = 1000;
$('div').not("div[class^=pp]").each(function() {
    $(this).css('zIndex', zIndexNumber);
    zIndexNumber -= 10;
});
