$('#Param999').change(function () {
    if (($(this).val() !=='list'))
    {
        $('.list-hidden').hide();
    }else{
        $('.list-hidden').show();
    }
}).change();
var value = $('label[for^="Param2"]').children('input:checked').val();
if (value == 1){
    $('.sub-list, .opt-prod').hide();
}
$('label[for^="Param2"]').click(function () {
    var value = $(this).children('input').val();
    console.log(value);
    if (value == 1){
        $('.sub-list, .opt-prod').slideUp();
    }else{
        $('.sub-list, .opt-prod').slideDown();
    }
});


