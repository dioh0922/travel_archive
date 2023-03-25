<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Flight;
use Src\Point;
$result = ["result" => 0, "list" => []];
try{
	$flight = new Flight();
	$point = new Point();
	$result["result"] = 1;
	$result["list"] = $flight->getAllRoute($_POST["departure_id"]);
	$result["departure"] = $point->getTargetPin($_POST["departure_id"]);
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
