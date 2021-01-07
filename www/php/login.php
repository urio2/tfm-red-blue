<?php
include 'LoggerConfig.php';

define('AES_256_CBC', 'aes-256-cbc');

$logger=Logger::getLogger("login");

$response = "ko";

if (isset($_POST['user']) && isset($_POST['pass'])){
  session_start();
  session_regenerate_id();
  if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
  }
  $token = $_SESSION['token'];
   
  include('dbConection.php');

  $user = mysqli_real_escape_string($db, $_POST['user']);
  $pass = mysqli_real_escape_string($db, $_POST['pass']);
  $ip = $_SERVER['REMOTE_ADDR'];


  $sql = "SELECT * FROM users WHERE user = '$user'";

  $users = array();
  if ($result = mysqli_query($db, $sql)) {
        while($row = $result -> fetch_object()) {
          array_push($users,$row);
        }   
  }

  if(count($users) > 0) {

    // $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $userPass = $users[0]->pass . ':' . base64_encode($iv);
    $parts = explode(':', $userPass);
    $userPassDecripted = openssl_decrypt($parts[0], AES_256_CBC, $encryption_key, 0, base64_decode($parts[1]));


    if($users[0]->intents > 3 && $users[0]->ip == $ip) {  // miramos si esta locked   
      //miramos la hora
      $date = date("d-m-Y H:i:s", strtotime('+30 minutes')); 
      $date_aux = date("d-m-Y H:i:s");
      if($users[0]->time != null && $users[0]->time < $date_aux && $userPassDecripted== $pass){        
        $response = "ok";       
      }
    }
    else if ($userPassDecripted == $pass) {
      $response = "ok"; 
    }

    if($response == "ok"){
      $response = "ok|" . $token; 
      $_SESSION["user"] = $user; 
      $logger->info("Login Success user: '$user'");

      $sql = "UPDATE users SET intents = 0 WHERE user = '$user'";
      $result = mysqli_query($db, $sql);
      $sql = "UPDATE users SET ip = '' WHERE user = '$user'";
      $result = mysqli_query($db, $sql);
      $sql = "UPDATE users SET time = '' WHERE user = '$user'";
      $result = mysqli_query($db, $sql);

    } else{
      $logger->error("Login Error user: '$user'");

      $l = $users[0]->intents; // poner el numero que sea en la BD, esto es el locked
      $locked = $l + 1;
      $currentDate = date("d-m-Y H:i:s");
     if($locked > 3) $currentDate = date("d-m-Y H:i:s", strtotime('+30 minutes'));  
    // entonces actualizamos los parametros de locked
      $sql = "UPDATE users SET intents = '$locked' WHERE user = '$user'";
      $result = mysqli_query($db, $sql);
      $sql = "UPDATE users SET ip = '$ip' WHERE user = '$user'";
      $result = mysqli_query($db, $sql);
      $sql = "UPDATE users SET time = '$currentDate' WHERE user = '$user'";
      $result = mysqli_query($db, $sql);
    }
  } else {
    $logger->error("Login Error user: '$user'");
  }
}

if ($db != null) {
  mysqli_close($db);
}

echo json_encode($response);

?>
