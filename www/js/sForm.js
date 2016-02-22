;(function ($) {
    function init(form, o) {
        var name = $('.name>input', form)
        , email = $('.email>input', form)
        , submit = $('[type="submit"]', form)
        , msg_success = $('.success', form).hide()
        , bl, vl;

        o = $.extend({
            ownerEmail: '#'
            , mailHandlerURL: '/newsletter/index/ajax/actionAjax/subscribe'
        }, o);
        submit.click(function (e) {
            e.preventDefault();
            vl = true;
            name.add(email).trigger('keyup');
            if (!$('.invalid', form).length)
                sendRQ();
            return false;
        });

        form[form.on ? 'on' : 'bind']('keyup', function (e) {
            if (e.keyCode === 13) {
                name.add(email).data({wtch: true}).trigger('keyup');
                if (!$('.invalid', form).length) {
                    sendRQ();
                    return false;
                } else {
                    $('.invalid', form).focus();
                }
            }
        });

        name.add(email)
            .data({wtch: true})
            .each(function () {
                var th = $(this), val = th.val();
                th.data({defVal: val})
                .focus(function () {
                    if (th.val() == val){
                        th.val('')
                        , th.parents('label').removeClass('invalid')
                        , th.data({wtch: false});
                    }
                })
                .blur(function () {
                    if (th.val() == '')
                        th.val(val);
                    //,th.parents('label').removeClass('invalid')
                    //else
                    th.data({wtch: true}).trigger('keyup');
                });
            })
        [name.on ? 'on' : 'bind']('keyup', function (e) {
            var th = $(this);
            if (th.data('wtch'))
                th.parents('label')[validate(th) ? 'removeClass' : 'addClass']('invalid');
        });

        function validate(el) {
            var rx = {
                "text": /^[a-zA-Z'][a-zA-Z-' ]+[a-zA-Z']?$/
                , "email": /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i
            }
            , val = el.val();
            (el.parents('label').hasClass('email')) && el.attr({'type': 'email'});
            return rx[el.attr('type')].test(val) && val !== el.data('defVal');
        }

        function sendRQ() {
            if (bl)
                return false;
            bl = true;
            $.ajax({
                type: "POST"
                , url: o.mailHandlerURL
                , data: {
                    name: name.length ? name.val() : email.val().split('@')[0]
                    , email: email.val()
                    , owner_email: o.ownerEmail
                    , sitename: o.sitename || o.ownerEmail.split('@')[1]
                }
                , success: function (e) {
                    bl = false;
                    console.log(e);
                    msg_success.slideDown(400, function () {
                        submit.focus();
                        name.add(email).data({wtch: true});
                        form.trigger('reset');

                        setTimeout(function () {
                            msg_success.fadeOut(400);
                        }, 5000);
                    });
                }
            });
        }
    }

    $.fn.sForm = function (o) {
        return this.each(function () {
            init($(this), o);
        });
    };

})(jQuery);
$(window).load(function () {
    $('#subscribe-form').sForm({
        ownerEmail: '#'
        , sitename: 'sitename.link'
    });
});