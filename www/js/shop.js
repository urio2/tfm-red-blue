$(document).ready(function(){
    getProducts();
});

function getProductsByPrice() {
	var filterValue1 = $('#value-lower')[0].textContent;
	var filterValue2 = $('#value-upper')[0].textContent;
	getProducts('price',filterValue1, filterValue2);
}

function getProductsBySearch() {
	var filterValue1 = $('#searchedProduct')[0].value;
	if (filterValue1) {
		getProducts('search',filterValue1);
	} else {
		getProducts();
	}
}

function getProducts(filterType, filterValue1, filterValue2) {
	$.ajax({
        type: 'GET',
        url: 'php/getProducts.php',
        data: {"filterType": filterType, "filterValue1": filterValue1, "filterValue2": filterValue2,"token":sessionStorage.getItem("token")},
        dataType: 'json',
        success: function(data){
            if (data != null) {
                var pieces = data.split("|");
                sessionStorage.setItem("token", pieces[1]);
            	$('#productListContainer').html(pieces[0]);
            } else {
            	$('#productListContainer').html("");
            }
        }
    });
}

function addToCart(id) {
    var self = this;
	$.ajax({
        type: 'POST',
        url: 'php/addProducts.php',
        data: {'productId': id, "token":sessionStorage.getItem("token")},
        dataType: 'json',
        success: function(data){
            if (data != null) {
                var pieces = data.split("|");
                sessionStorage.setItem("token", pieces[1]);
                self.swal("Producto añadido a la cesta");
            }
        }
    });
}

function showDetails(id) {
    window.location.href = 'product-details.html?productId=' + id;
}
