<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Point;
$result = ["result" => 0, "list" => []];
try{
	$point = new Point;
	$result["result"] = 1;
	$result["list"] = $point->getAllPin($_POST["category"]);
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
