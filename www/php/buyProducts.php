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
session_start();
if($_SESSION['token'] != $_POST['token']){
  $user = null;
}
else {
 
 $user = $_SESSION["user"];
}
if ($user != null) {
	$logger=Logger::getLogger("buyProducts");

	include('dbConection.php');

	
	$sql = "UPDATE cart SET bought = 1 WHERE user = '$user'";

	if ($result = mysqli_query($db, $sql)) {   
		$response = "ok";
		$logger->info("Cart updated to bough for user '$user'."); 
	} else {
	 	$response = "ko";
		$logger->error("Updating to bough failed for cart of user '$user'.");
	}
	mysqli_close($db);

	echo json_encode($response);
}

?>
