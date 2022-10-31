<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
session_start();
use Src\Access;
$result = ["result" => 0];
$access = new Access();
$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
$env->load();

try{
	if(!array_key_exists("login", $_SESSION) || $_SESSION["login"] == null){
		throw new Exception("session expire");
	}
	$today = date("Y-m-d");
	$current_access = $access->getCurrentCount($today);
	if($current_access->access_count >= $_ENV["G_ACCESS_LIMIT"]){
		$result["result"] = -1;
		$result["message"] = "google api access limit";
	}else{
		$access->addAccess($today);
		$result["result"] = 1;
	}
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>
