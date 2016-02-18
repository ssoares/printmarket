jQuery(function ($)
{
    // Variable de vitesse d'animation'
    var fadeInSpeed = 350;
    var fadeOutSpeed = 350;

    $('document').ready(function()
    {

        // Classe possible :
        // ".swap-image" : sur l'image directement
        // ".swap-image-conteneur" : sur le contenur de l'image
        // Les classes pécédentes combinés avec ".selected"


        // Permet de faire une permutation sur le survol d'une image
        // Pour ce faire :
        // 1- Donner la classe "swap-image" à l'image
        // 2- Créer des images une avec "_up" et une avec "_over" avant le point de l'extension (ex: image_up.png et image_over.png)
        //


        if($('img.swap-image').length > 0)
        {
            // Permet de changer l'image en over si l'image a la classe "selected""
            if($('img.swap-image.selected').length > 0)
            {
                var imageSelectedOver = $('img.swap-image.selected').attr("src").replace("_up.", "_over.");
                $('img.swap-image.selected').attr("src", imageSelectedOver);
            }


            $('img.swap-image').not(".selected")
                .mouseover(function()
                {
                    var imageOver = $(this).attr("src").replace("_up.", "_over.");

                    if (imageOver.indexOf("_over.") > 0)
                        $(this).attr("src", imageOver);

                })
                .mouseleave(function()
                {
                    var imageUp = $(this).attr("src").replace("_over.", "_up.");

                    if (imageUp.indexOf("_up.") > 0)
                        $(this).attr("src", imageUp);

                });
        }

        // Permet de faire une permutation sur le survol d'une image
        // Pour ce faire :
        // 1- Donner la classe "swap-image-conteneur" au Conteneur (ex: au <li> ou à la <div>)
        // 2- Créer des images une avec "_up" et une avec "_over" avant le point de l'extension (ex: image_up.png et image_over.png)

        if($('.swap-image-conteneur').length > 0)
        {
            // Permet de changer l'image en over si le conteneur est "selected"
            if($('.swap-image-conteneur.selected').length > 0)
            {
                var imageConteneurSelectedOver = $('.swap-image-conteneur.selected').find("img").attr("src").replace("_up.", "_over.");
                $('.swap-image-conteneur.selected').find("img").attr("src", imageConteneurSelectedOver);
            }


            $('.swap-image-conteneur').not(".selected")
                .mouseover(function()
                {
                    var imageOver = $(this).find("img").attr("src").replace("_up.", "_over.");

                    if (imageOver.indexOf("_over.") > 0)
                        $(this).find("img").attr("src", imageOver);

                })
                .mouseleave(function()
                {
                    var imageUp = $(this).find("img").attr("src").replace("_over.", "_up.");

                    if (imageUp.indexOf("_up.") > 0)
                        $(this).find("img").attr("src", imageUp);

                });
        }


        $(window).load(function()
        {
            if($('img.swap-image-fade').length > 0 )
            {
                // Permet de changer l'image en over si le conteneur est "selected"
                if($('img.swap-image-fade.selected').length > 0)
                {
                    var imageFadeSelectedOver = $('img.swap-image-fade.selected').attr("src").replace("_up.", "_over.");
                    $('img.swap-image-fade.selected').attr("src", imageFadeSelectedOver);
                }

                // clone image
                $('img.swap-image-fade').not(".selected").each(function()
                {
                    var imageOver = $(this).attr("src").replace("_up.", "_over.");

                    var el = $(this);
                    el.css({"position":"absolute"}).wrap("<div class='img_wrapper' style='display: block'>").clone().addClass('img_over').css({"position":"absolute","z-index":"998","opacity":"1"}).insertBefore(el).queue(function()
                    {
                        var el = $(this);

                        el.parent().css({"width":this.width,"height":this.height});
                        el.dequeue();
                    });

                    this.src = imageOver;
//                    $(this).css('opacity',0);
                });

                // Fade image

                // Fait disparait l'image de premier plan pour affiche celle en dessous
                $('img.swap-image-fade').not(".selected").mouseover(function()
                {
                   // $(this).not(".img_over").fadeIn(1);
                    $(this).stop().animate({opacity:0}, fadeInSpeed);
                })

                // ramène l'image original en premier plan
                $('.img_over').not(".selected").mouseout(function(){
                    $(this).parent().find('img:first').stop().animate({opacity:1}, fadeOutSpeed);
                });

            }
            else if($('.swap-image-conteneur-fade').length > 0 )
            {
                // Permet de changer l'image en over si le conteneur est "selected"
                if($('.swap-image-conteneur-fade.selected').length > 0)
                {
                    var imageConteneurSelectedOver = $('.swap-image-conteneur-fade.selected').find("img").attr("src").replace("_up.", "_over.");
                    $('.swap-image-conteneur-fade.selected').find("img").attr("src", imageConteneurSelectedOver);
                }

                // clone image
                $('.swap-image-conteneur-fade').not(".selected").find("img").each(function()
                {
                    //console.log($(this).attr("alt"));

                    var imageOver = $(this).attr("src").replace("_up.", "_over.");
                    //console.log(imageOver);

                    var el = $(this);
                    el.css({"position":"absolute"}).wrap("<li class='img_wrapper' style=''>").clone().addClass('img_over').css({"position":"absolute","z-index":"998"}).insertBefore(el).queue(function()
                    {
                        var el = $(this);

                        el.parent().css({"width":this.width,"height":this.height});
                        el.dequeue();
                    });

                    this.src = imageOver;
                    el.css('opacity',0);

                });

                if($('.swap-image-conteneur-fade').length > 0 )
                {
                    // Déplacer les <A> à l'intérieur des <li>
                    $('.swap-image-conteneur-fade').find("a").each(function()
                    {

                        var aContent = $(this).children();

                        var newLink = $(this).clone();

                        newLink.html('');

                        newLink.html(aContent.html());

                        aContent.html('');

                        aContent.append(newLink);

                        $(this).find('.img_wrapper').unwrap("a");

                    });
                }

                // Fade image

                // Fait disparait l'image de premier plan pour affiche celle en dessous
                $('.swap-image-conteneur-fade').not(".selected").mouseover(function()
                {
                    var imgUp = $(this).find("img.img_over");
                    var imgOver = $(this).find("img:last");

                    imgOver.stop().animate({opacity:1}, fadeInSpeed);
                    imgUp.stop().animate({opacity:0}, fadeInSpeed);
                }).mouseout(function(){
                    var imgUp = $(this).find("img.img_over");
                    var imgOver = $(this).find("img:last");

                    imgOver.stop().animate({opacity:0}, fadeOutSpeed);
                    imgUp.stop().animate({opacity:1}, fadeOutSpeed);
                });

            }

        });

    });

});