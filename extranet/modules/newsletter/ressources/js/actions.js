$(document).ready(function(){
    // function to validate email test on the fly
    $('#email').keyup(
        function (e) {
            email = $("#email").val();
            if(email != 0){
                if(isValidEmailAddress(email)){
                    $('#sendEmailButton').removeAttr("disabled");
                }
                else{
                    $('#sendEmailButton').attr("disabled", true);
                }
            }
            else{
                $('#sendEmailButton').attr("disabled", true);     
            }
        }
    );
    
    $('#sendEmailButton').click(
        function (e) {
            url             = $("#ajaxLink").val();
            releaseID       = $("#releaseID").val();
            emailTest       = $("#email").val();
            $.getJSON(url,{releaseID : releaseID, email : emailTest},
                function(data){
                    $('#email').val("");
                    $('#sendEmailButton').attr("disabled", true);
                    $('#messageEmailSendConfirmation').slideDown("slow");
                }
            );
        }
    );
});


function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}