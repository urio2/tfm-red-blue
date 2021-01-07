<?php
include 'LoggerConfig.php';

function is_session_started()
{
return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
}

// Example
//if ( is_session_started() === FALSE )  echo json_encode("ko");
//session_start();

//$user = $_SESSION["user"];

session_start();
if($_SESSION['token'] != $_POST['token']){
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
	$logger=Logger::getLogger("addProducts");

	if (isset($_POST['productId'])){
		
		
		include('dbConection.php');

		$productId = mysqli_real_escape_string($db, $_POST['productId']);
		

		$sql = "INSERT INTO cart (productId, user) VALUES ('$productId', '$user')";

		if ($result = mysqli_query($db, $sql)) {   
			$response = "ok";
			$logger->info("Product '$productId' inserted to database."); 
		} else {
		 	$response = "ko";
			$logger->error("Error inserting '$productId' to database.");
		}
		mysqli_close($db);

		echo json_encode($response . "|" . $_SESSION['token']);
	} else {
		$response = "ko";
	}
}
?>
