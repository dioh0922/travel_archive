<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Flight;
$result = ["result" => 0, "list" => []];
try{
	$flight = new Flight;
	$result["result"] = 1;
	$result["list"] = $flight->getAllRoute($_POST["flight_id"]);
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
