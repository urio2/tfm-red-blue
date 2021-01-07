$(document).ready(function(){
	var productId = getUrlVars()["productId"];
	$.ajax({
        type: 'GET',
        url: 'php/getProductDetails.php?token=' + sessionStorage.getItem("token"),
        data: {"productId": productId},
        dataType: 'json',
        success: function(data){
            if (data != null) {
		var pieces = data.split("|");
                sessionStorage.setItem("token", pieces[1]);
            	$('#productDetailContainer').html(pieces[0]);
            	getProductOpinions(productId);
            } else {
            	$('#productDetailContainer').html("");
            }
        }
    });
});

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function getProductOpinions(productId) {
	$.ajax({
        type: 'GET',
        url: 'php/getProductOpinions.php?token=' + sessionStorage.getItem("token"),
        data: {"productId": productId},
        dataType: 'json',
        success: function(data){
            if (data != null) {
		var pieces = data.split("|");
                sessionStorage.setItem("token", pieces[1]);
            	$('#productOpinionsContainer').html(pieces[0]);
            } else {
            	$('#productOpinionsContainer').html("");
            }
        }
    });
}

function addOpinionToProduct(productId) { 
    var opinion = $("#productOpinion").val();
    $.ajax({
        type: 'POST',
        url: 'php/addOpinionToProduct.php',
        data: {"productId": productId, "opinion": opinion,"token": sessionStorage.getItem("token")},
        dataType: 'json',
        success: function(data){
            var pieces = data.split("|");
            sessionStorage.setItem("token", pieces[1]);
            if (pieces[0] == 'ok') {
            	location.reload();
            }
        }
    });

}
