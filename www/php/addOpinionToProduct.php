<?php
include 'LoggerConfig.php';
function is_session_started()
{
if ( php_sapi_name() !== 'cli' ) {
if ( version_compare(phpversion(), '5.4.0', '>=') ) {
return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
} else {
return session_id() === '' ? FALSE : TRUE;
}
}
return FALSE;
}

// Example
//if ( is_session_started() === FALSE ) echo json_encode("ko");
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

	$logger=logger::getLogger("addOpinionToProduct");

	if (isset($_POST['productId']) && isset($_POST['opinion']) && $_POST['opinion'] != ''){
		
		
		include('dbConection.php');

		$productId = mysqli_real_escape_string($db, $_POST['productId']);
		$opinion = mysqli_real_escape_string($db, $_POST['opinion']);
		$aux = htmlspecialchars($productId);		
		$aux1 = htmlspecialchars($opinion); 
		$aux2 = htmlspecialchars($user); 

		$sql = "INSERT INTO productOpinions (productId, user, opinion) VALUES ('$aux', '$aux2', '$aux1')";

		if ($result = mysqli_query($db, $sql)) {   
			$response = "ok";
			$logger->info("Opinion '$opinion' inserted to database") ;
		} else {
		 	$response = "ko";
			$logger->error("Error inserting opinion to database");
		}
		mysqli_close($db);

		echo json_encode($response . "|" . $_SESSION['token']);
	} else {
		$response = "ko";
	}
}
?>
