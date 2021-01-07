$(document).ready(function(){
    $.ajax({
        type: 'GET',
        url: 'php/session.php',
        data: {},
        dataType: 'json',
        success: function(data){
            if (data == null) {
                window.location.href = "login.html"
            }
        }
    });
});