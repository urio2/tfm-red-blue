<?php
//session_start();
//$user = $_SESSION['user'];

session_start();
if($_SESSION['token'] != $_GET['token']){
  $user = null;
}
else {
 $user = $_SESSION["user"];
 if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
 } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
 }
}

if ($user != null) {

    include('dbConection.php');

    $sql = "SELECT imagePath, name, price, count(*) as count FROM cart JOIN products on 
    cart.productId = products.id where user = '$user' AND bought = 0 group by productId";

    $a = array();
    if ($result = mysqli_query($db, $sql)) {
    	$cartProductHeader=file_get_contents('../html/cartProductHeader.html');
        while($row = $result -> fetch_object()) {

        	$price = $row->price;
        	$count = $row->count;
        	$total = $price * $count;

            $cartProduct=file_get_contents('../html/cartProduct.html');
            $cartProduct=str_replace("##NAME##",$row->name, $cartProduct);
            $cartProduct=str_replace("##PRICE##", $price, $cartProduct);
            $cartProduct=str_replace("##COUNT##", $count, $cartProduct);
            $cartProduct=str_replace("##IMAGE##",$row->imagePath ,$cartProduct);
            $cartProduct=str_replace("##TOTAL##",$total ,$cartProduct);

            $cartProducts=$cartProducts.$cartProduct;
            array_push($a,$row);
        }  
    }

    mysqli_close($db);
    // echo json_encode($a); 
    echo json_encode($cartProductHeader.$cartProducts . '|' . $_SESSION['token']);
}
?>
