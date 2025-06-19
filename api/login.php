<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Login;

header('Content-Type: application/json');

$result = ["result" => 0];

try {
  $login = new Login();
  if($login->validateCsrf()){
    if($login->login($_POST["pass"])){
      $result["result"] = 1;
    }
  }

}catch(Exception $e){
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("getFlightRoute.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>