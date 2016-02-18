/*$(document).ready(function(){
    $('ul[id^=ul_submenu] li.selected').each(function(){
        var child_ul = $(this).children('ul:first');

        if( !child_ul.hasClass('open') )
        {
            child_ul.addClass('open');
            $(this).children('a').addClass('hasChildren');
        }

        $(this).parents('li').each(function(){
            var current_li = $(this);
            if( !current_li.hasClass('open_li') )
                current_li.addClass('open_li');
        });

        $(this).parents('ul').each(function(){
            var current_ul = $(this);
            if( !current_ul.hasClass('open') )
                current_ul.addClass('open');
        });

        var parentid = $(this).parents('li.level-1:first').attr('id');
        $('ul[id^=ul_submenu] li[id='+parentid+']').addClass('selected');
        if ($('.nav li[id='+parentid+']').length)
        $('.nav li[id='+parentid+']').addClass('selected');
    });

    var nbSelec = $('.collectionsSelected ul li.selected').length;

    if (nbSelec > 1)
    {
        $('.collectionsSelected').addClass('selected');
        $('.collectionsSelected ul li.selected').each(function(){
            $(this).removeClass('selected');
            var href = jQuery(location).attr('href');

            var pattern = "/collection/";
            var findUrl = href.match(pattern);
            if (findUrl)
            {
                var subUrl  = href.substring(findUrl.index + 1, href.length);
                var urlVals = subUrl.split('/') ;
                var curHref = $(this).find('a').attr('href');

                var hasCollection = curHref.match("/" + urlVals[1] +"$");

                if (hasCollection)
                    $(this).addClass('selected');
            }

        });
    }

    var is_selected = true;
    $('ul[id^=ul_nav] > li a').not("ul[id^=ul_nav] li ul li a").each(function()
    {
        var ul  = $(this).parent().children('ul');
        var div = $(this).parent().children('div');
        ul.css('visibility', 'hidden');
        div.css('visibility', 'hidden');

        if (!$(this).parent().hasClass('NoSecondLevel'))
        {
            $(this).mouseenter(function()
            {
                var next_ul = ul.css('visibility', 'visible');
                div.css('visibility', 'visible');

                subMenu.show(next_ul, div);
                subMenu.hide(next_ul, div);

            });
        }

        $(this).mouseleave(function()
        {
            ul.css('visibility', 'hidden');
            div.css('visibility', 'hidden');
            $(this).parent().removeClass('hover');

        });
    });

    $(".top-menu li").not('.selected').mouseover(function(){
        $(this).prev('.left').addClass('selectedLeft');
        $(this).next('.right').addClass('selectedRight');
    });

    $(".top-menu li").not('.selected').mouseout(function(){
        $(this).prev('.left').removeClass('selectedLeft');
        $(this).next('.right').removeClass('selectedRight');
    });

    var subMenu = {
        show: function(ul, div){

//            ul.each(function()
//            {
//                var elem = $(this);
                ul.mouseenter(function()
                {
                    ul.css('visibility', 'visible');
                    div.css('visibility', 'visible');

                    if( !$(this).parents('li').hasClass('selected') )
                    {
                        $(this).parents('li').addClass('selected');
                        is_selected = false;
                    }
                    if( !$(this).parents('li.level-1').hasClass('hover') )
                    {
                        $(this).parents('li.level-1').addClass('hover');
                    }
                });
                div.mouseenter(function()
                {
                    if( !$(this).parents('li').hasClass('selected') )
                    {
                        $(this).parents('li').addClass('selected');
                        is_selected = false;
                    }
                    if( !$(this).parents('li.level-1').hasClass('hover') )
                    {
                        $(this).parents('li.level-1').addClass('hover');
                    }
                    $(this).css('visibility', 'visible');
                    ul.css('visibility', 'visible');
                });

//            });
        },
        hide: function(ul, div){
//            ul.each(function()
//            {
//                var elem = $(this);
                ul.mouseleave(function()
                {
                    ul.css('visibility', 'hidden');
                    div.css('visibility', 'hidden');

                    if( is_selected == false )
                    {
                        is_selected = true;
                        $(this).parents('li').removeClass('selected');
                    }
                });
                div.mouseleave(function()
                {
                    if( is_selected == false )
                    {
                        is_selected = true;
                        $(this).parents('li').removeClass('selected');
                    }
                    $(this).css('visibility', 'hidden');
                    ul.css('visibility', 'hidden');
                    div.parent('li.level-1').removeClass('hover');
                });
//            });
        }
    }

    if($('#ul_submenu').length > 0)
    {
        var subName = $('#ul_submenu').find('.selected').attr("id");

        if(subName != undefined)
        {
            var subID = subName.split('-');
            $('#parentid-' + subID[1]).parent().parent().addClass('selected');
        }
        setSelected($('#ul_submenu'))
    }

    $('.mouseOverTrigger').mouseenter(function(){
        $(this).prev().addClass('mouseOver');
        $(this).next().addClass('mouseOver');
        $(this).addClass('mouseOver');

    }).mouseleave(function(){
        $(this).prev().removeClass('mouseOver');
        $(this).next().removeClass('mouseOver');
        $(this).removeClass('mouseOver');
    });

//    Set the select status to the current tab site in the top menu (e.q. CSSS)
    $('.mouseOverTrigger').each(function(){
        var location = window.location;
        var link = $(this).children('a').attr('href');
        var hasSelect = $(this).hasClass('selected');
        var isSelected = false;

        if (link.lastIndexOf('/') > 6)
            link = link.substring(0, link.lastIndexOf('/'));

        if (!hasSelect)
        {
            var currentDomain = location.host;
            var fullDomain = location.protocol + '//' + currentDomain;

            if($('#ul_submenu').length > 0)
            {
                var compareHref  = $('#ul_submenu').find('li.level-1.selected a').attr("href");
                if(compareHref === link)
                    isSelected = true;
                if (!isSelected)
                {
                    if (link === currentDomain || link === fullDomain)
                        isSelected = true;
                }
            }
            else if (!isSelected)
            {
                if (link === currentDomain || link === fullDomain)
                    isSelected = true;
            }
        }
        if ( isSelected)
        {
            $(this).siblings().removeClass('selected');
            $(this).prev('.left').addClass('selected');
            $(this).next('.right').addClass('selected');
            $(this).addClass('selected');
            $('ul[id^=ul_footer-menu] li[id$=' +  + ']').addClass('selected');
        }
    });

});

$(window).load(function(){
    var subMenu = $('#ul_nav li').find('.dropdown');
    subMenu.each(function()
    {
        subMenu = $(this);
        if (subMenu.length)
        {
            var maxWidth = subMenu.outerWidth();
            var children = subMenu.children().children();
            var nbCells = children.length;
            if (nbCells < 3)
            {
                var cWidth = subMenu.children().find('li:first').outerWidth();
                if (maxWidth > cWidth)
                 var width = maxWidth - (cWidth * (3 - nbCells));
                subMenu.css('width', width);
                children.each(function(){
                    switch (nbCells)
                    {
                        case 1 :
                            $(this).css('width', width);
                            break;
                        case 2 :
                            $(this).css('width', '40%');
                            break;
                        default:
                            break;

                    }
                });
            }
            if (!subMenu.hasClass('leftPos'))
            {
                var container = $('#ul_nav');
                var contWidth = container.width();
                var pos = container.offset();
                var limit = contWidth + pos.left;
                var subMenuPos = subMenu.offset();
                var left = subMenuPos.left;
                var width = subMenu.innerWidth();
                var diff = left + width;

                if(diff > limit)
                {
                    var leftPos = (limit - diff) + 3;
                    subMenu.css({ 'left': leftPos});
                    subMenu.addClass('leftPos');
                }
            }
        }

    });
});
function setSelected(obj){
    var href = jQuery(location).attr('href');
    var mainMenu = $('ul[id^=ul_nav] > li a');
    var footerMenu = $('ul[class^=footer-menu] > li a');
    obj.children().removeClass('selected');
    obj.children().each(function(){
        var thisHref = $(this).children('a').attr('href');
        var selected = $('#currentSelected').val();
        var containStr = href.search(thisHref);
        if (containStr > 0)
        {
            if (selected)
                containStr = thisHref.search(selected);
            if (containStr > 0)
            {
                $(this).addClass('selected');
                $(this).parents('li.level-1').addClass('selected');
            }
            else if ($(this).hasClass('level-1'))
            {
                $(this).addClass('selected');
            }
            mainMenu.each(function(){
                var url = $(this).attr('href');
                if (url == thisHref)
                {

                    $(this).parent().siblings().removeClass('selected');
                    $(this).parent().addClass('selected');
                }
            });

            footerMenu.each(function(){
                var url = $(this).attr('href');
                if (url == thisHref)
                {

                    $(this).parent().siblings().removeClass('selected');
                    $(this).parent().addClass('selected');
                }
            });
        }

        if ($(this).children().length > 0)
        {
            setSelected($(this).children())
        }
    });
}*/
