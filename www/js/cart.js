$(document).ready(function(){
    $.ajax({
        type: 'GET',
        url: 'php/getCartProducts.php?token=' + sessionStorage.getItem("token"),
        data: {},
        dataType: 'json',
        success: function(data){
            // console.log(data);
            if (data != null) {
                var total = 0;
		var pieces = data.split("|");
                sessionStorage.setItem("token", pieces[1]);
                
                 $('#cartProductContainer').html(pieces[0]);
                 for (i = 0; i < $('.total').length; i++) {
                    total = total + parseInt($('.total')[i].textContent.slice(0, -1));
                 }
                $('#cartTotal').html(total + ' €');
		
            }
            else {
                $("#errorLoginMsg")[0].textContent = 'Get Card Products failed';
            }
        }
    });
});

function buyProducts() {
    $.ajax({
        type: 'POST',
        url: 'php/buyProducts.php',
        data: {'token': sessionStorage.getItem("token")},
	dataType: 'json',
        success: function(data){
            console.log(data);
           window.location.href = "shop.html";
        }
    });
}
