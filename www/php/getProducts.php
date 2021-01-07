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

    $sql = "SELECT * FROM products";

    switch ($_GET['filterType'])
    {
        case 'category':
        	$filterValue1 = mysqli_real_escape_string($db, $_GET['filterValue1']);
        	$sqlCondition = $sqlCondition." WHERE category = '$filterValue1'";
        	break;
        case 'price':
        	$filterValue1 = mysqli_real_escape_string($db, $_GET['filterValue1']);
        	$filterValue2 = mysqli_real_escape_string($db, $_GET['filterValue2']);
        	$sqlCondition = $sqlCondition." WHERE price >= '$filterValue1' AND price <= '$filterValue2'";
        	break;
        case 'search':
        	$filterValue1 = mysqli_real_escape_string($db, $_GET['filterValue1']);
        	$sqlCondition = $sqlCondition." WHERE name like '%$filterValue1%'";
        	break;
    }
    $sql = $sql.$sqlCondition;


    if ($result = mysqli_query($db, $sql)) {
        while($row = $result -> fetch_object()) {

            $product=file_get_contents('../html/product.html');
            $product=str_replace("##ID##",$row->id ,$product);
            $product=str_replace("##NAME##",$row->name ,$product);
            $product=str_replace("##PRICE##",$row->price ,$product);
            $product=str_replace("##IMAGE##",$row->imagePath ,$product);

            $products=$products.$product;
        }  
    }

    mysqli_close($db);
    echo json_encode($products . "|" . $_SESSION['token']); 
}
?>
