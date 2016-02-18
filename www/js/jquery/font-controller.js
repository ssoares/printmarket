/**
 * Font Controller
 * For creating a font size changer interface with minimum effort
 * Copyright (c) 2009 Hafees (http://cool-javascripts.com)
 * License: Free to use, modify, distribute as long as this header is kept :)
 *
 */

/**
 * Required: jQuery 1.x library, 
 * Optional: jQuery Cookie Plugin (if used, the last used font size will be saved)
 * Usage: (For more details visit 
 * This function can be called inside a $(document).ready()
 * Eg: fontSize("#controls", "#content", 9, 12, 20); where,
 * #controls - where control is the element id, where the controllers will be created.
 * #content - for which element the font size changes to apply. In this case font size of content div will be changed
 * 9 - minimum font size
 * 12 - default font size
 * 20 - maximum font size
 * 
 */

function fontSize(container, target, minSize, defSize, maxSize, baseUrl) {
	/*Editable settings*/
	/*var minCaption = "btn-13px.png"; //title for smallFont button
	var defCaption = "btn-15px.png"; //title for defaultFont button
	var maxCaption = "btn-17px.png"; //title for largefont button*/
	
	
	//Now we'll add the font size changer interface in container
	smallFontHtml = "<li><a href='javascript:void(0);' class='smallFont' title=''><img src='" + baseUrl + "/themes/default/images/common/btn-13px.png' alt='' border='0'></a></li> ";
	defFontHtml = "<li><a href='javascript:void(0);' class='defaultFont' title=''><img src='" + baseUrl + "/themes/default/images/common/btn-15px.png' alt='' border='0'></a></li> ";
	largeFontHtml = "<li><a href='javascript:void(0);' class='largeFont' title=''><img src='" + baseUrl + "/themes/default/images/common/btn-17px.png' alt='' border='0'></a></li> ";
	$(container).html(smallFontHtml + defFontHtml + largeFontHtml);
	
	//Read cookie & sets the fontsize
	if ($.cookie != undefined) {
		var cookie = target.replace(/[#. ]/g,'');
		var value = $.cookie(cookie);
		if (value !=null) {
			$(target).css('font-size', parseInt(value));
		}
	}
		
	//on clicking small font button, font size is decreased by 2px
	$(container + " .smallFont").click(function(){ 
		curSize = parseInt($(target).css("font-size"));
		//newSize = curSize - 2;
        newSize = minSize;
		if (newSize >= minSize) {
			$(target).css('font-size', newSize);
		} 
		if (newSize <= minSize) {
			$(container + " .smallFont").addClass("sdisabled");
		}
		if (newSize < maxSize) {
			$(container + " .largeFont").removeClass("ldisabled");
		}
		updatefontCookie(target, newSize); //sets the cookie 
		
	});

	//on clicking default font size button, font size is reset
	$(container + " .defaultFont").click(function(){
		$(target).css('font-size', defSize);
		$(container + " .smallFont").removeClass("sdisabled");
		$(container + " .largeFont").removeClass("ldisabled");
		updatefontCookie(target, defSize);
	});

	//on clicking large font size button, font size is incremented by 2 to the maximum limit
	$(container + " .largeFont").click(function(){
		curSize = parseInt($(target).css("font-size"));
		//newSize = curSize + 2;
        newSize = maxSize;
		if (newSize <= maxSize) {
			$(target).css('font-size', newSize);
		} 
		if (newSize > minSize) {
			$(container + " .smallFont").removeClass("sdisabled");
		}
		if (newSize >= maxSize) {
			$(container + " .largeFont").addClass("ldisabled");
		}
		updatefontCookie(target, newSize);
	});

	function updatefontCookie(target, size) {
		if ($.cookie != undefined) { //If cookie plugin available, set a cookie
			var cookie = target.replace(/[#. ]/g,'');
			$.cookie(cookie, size);
		} 
	}
}