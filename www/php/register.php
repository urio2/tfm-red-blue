<?php
include 'LoggerConfig.php';

define('AES_256_CBC', 'aes-256-cbc');

$logger=Logger::getLogger("register");


if (isset($_POST['user']) && isset($_POST['pass'])){
  
  session_start();

  include('dbConection.php');

  $user = mysqli_real_escape_string($db, $_POST['user']);

  $pass = mysqli_real_escape_string($db, $_POST['pass']);
  $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
  $pass = openssl_encrypt($pass, AES_256_CBC, $encryption_key, 0, $iv);
  $pass = $pass . ':' . base64_encode($iv);

  $creditCard = openssl_encrypt($creditCard, AES_256_CBC, $encryption_key, 0, $iv);
  $creditCard = $creditCard . ':' . base64_encode($iv);
  
  $creditCard = mysqli_real_escape_string($db, $_POST['creditCard']);


  $sql = "INSERT INTO users (user, pass, creditCard) VALUES ('$user', '$pass', '$creditCard')";

  if ($result = mysqli_query($db, $sql)) {   
		$response = "ok";
    $_SESSION["user"] = $user;
    $logger->info("user '$user' registered");	      
  } else {
    $response = "ko";
    $logger->error("Error registering user: '$user'.");
  }
  mysqli_close($db);

} else {
  $response = "ko";
  $logger->error("Parameters errors.");
}

echo json_encode($response);

?>
