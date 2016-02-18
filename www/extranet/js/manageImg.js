(function($) {
    $.fn.manageImg = function(options){
        var defaults = {
            baseUrl: '',
            containerId: "fieldset-productFormRight",
            imgZone: $('#additionalImg'),
            imgList: null,
            imagesPath:'',
            nbImg: 0,
            nbImgLimit: 2,
            noPicSrc: '/icons/image_non_ disponible.jpg',
            labelOrder:$('<label>').text('Ordre'),
            fieldOrder:$('<input>').attr({type: 'text',id: '',name: '', 'class':'numeric'}),
            defaultNames: ['P_IsNewImage', 'P_Photo_tmp', 'P_Photo_original', 'P_Photo_preview', 'P_Photo']
        };
        var o = $.extend({},defaults,options);
        o.containerSrc = $('#' + o.containerId).children('dl');
        var init= {
            fill:function(){
                $.each(o.imgList, function(n, img){
                    container.add(img);
                });
                init.countImg();
                if (o.nbImg == o.nbImgLimit)
                    $('#moreImg').hide();
            },
            countImg: function(){
                o.nbImg = o.imgZone.children('dl').length;
            }
        };
        var container  = {
            add : function(img){
                var clone = container._clearDefaults(o.containerSrc.clone(), img);
                clone.hide();
                o.imgZone.append(clone);
                clone.delay(800).show();
                init.countImg();
                if (o.nbImg == o.nbImgLimit)
                    $('#moreImg').hide();
            },
            _clearDefaults: function(clone, img){
                var cleanDefaults = {};
                var newPos = {};
                init.countImg();
                var ids = new Array();
                var newIds = new Array();
                var name = '';
                var index = o.nbImg + 1;
                var imgLbl = '';
                var isImage = false;
                var seq = 0;
                if (img != undefined)
                    isImage = true;
                clone.children().each(function(n){
                    var link = {};
                    var del  = {};
                    var children = {};
                    var dd   = $(this);
                    if(dd.is('input')){
                        var children = dd;
                    }else{
                        var children = dd.children('input');
                    }
                    var tmpId = children.attr('id')
                    name = children.attr('name');
                    ids.push(tmpId);
                    var newId = tmpId + '_' + (index);
                    var tmpVal = 'moreImg['+index+'][' + o.defaultNames[n] + ']';
                    var newName = children.attr('name').replace(o.defaultNames[n], tmpVal);
                    newIds.push(newId);

                    children.attr('id', newId);
                    children.attr('name', newName);
                    var imgPath = '';
                    if (isImage)
                    {
                        if (img.CPI_Img)
                        {
                            imgLbl = img.CPI_Img;
                            imgPath = o.imagesPath + img.CPI_Img;
                            seq = img.CPI_Seq;
                        }
                        else
                        {
                            var fieldPath = o.defaultNames[1];
                            var fieldName = o.defaultNames[4];
                            var fieldSeq = o.defaultNames[4] + '_Seq';
                            imgLbl = img[fieldName];
                            imgPath = img[fieldPath];
                            seq = img[fieldSeq];
                        }
                    }

                    if (children.attr('type') == 'image')
                    {
                        if (isImage)
                            children.attr('src', imgPath) ;
                        else
                            children.attr('src', o.baseUrl + o.noPicSrc) ;
                    }
                    else if (children.attr('type') == 'hidden')
                    {
                        if (isImage)
                            children.val(img[tmpId]);
                    }
                    if (dd.children('a').length)
                    {
                        link = dd.children('a').attr('href');
                        $.each(ids, function(i, idStr){
                            var regexStr = "'" + idStr + "'";
                            var reg = new RegExp(regexStr, "g");
                            link = link.replace(reg, "'" + newIds[i] + "'" );
                        });
                        dd.children('a').attr('href', link);
                        children.attr('associatedelement', newIds[3]);
                        children.removeAttr('onchange');
                        if (isImage)
                            children.val(imgLbl);
                        else
                            children.val('');
                    }
                    if (dd.children('img').length)
                    {
                        var onclick = function(){separateImage(newIds[3],  o.baseUrl + o.noPicSrc , newIds[4])};
                        dd.children('img').removeAttr('onclick');
                        dd.children('img').bind('click', onclick);
                    }

                });
                cleanDefaults = clone;
                newPos = o.fieldOrder.clone();
//                o.labelOrder.appendTo(cleanDefaults);
                newPos.attr('id', o.defaultNames[4] + '_Seq');
                newPos.attr('name', name.replace(o.defaultNames[4], 'moreImg['+index+'][' + newPos.attr('id') + ']'));
                if (isImage)
                    newPos.val(seq);
                else
                    newPos.val(index * 100);

                newPos.appendTo(cleanDefaults);

                return cleanDefaults;
            }
        };
        init.fill();
        $('#moreImg').click(function(e){e.preventDefault();container.add();});

    }
})(jQuery);