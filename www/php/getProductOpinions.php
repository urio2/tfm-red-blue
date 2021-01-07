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

	$sql = "SELECT * FROM productOpinions";

	$productId = mysqli_real_escape_string($db, $_GET['productId']);
	$sqlCondition = $sqlCondition." WHERE productId = '$productId' order by timestamp desc";

	$sql = $sql.$sqlCondition;


	if ($result = mysqli_query($db, $sql)) {
	    while($row = $result -> fetch_object()) {

	        $productOpinion=file_get_contents('../html/productOpinion.html');
	        $productOpinion=str_replace("##USER##",$row->user ,$productOpinion);
	        $productOpinion=str_replace("##OPINION##",$row->opinion ,$productOpinion);

	        $date = $row->timestamp;
	        $productOpinion=str_replace("##DATE##",$date ,$productOpinion);


	        $productOpinions=$productOpinions.$productOpinion;
	    }  
	}

	mysqli_close($db);
	echo json_encode($productOpinions . "|" . $_SESSION['token']);
}
?>
