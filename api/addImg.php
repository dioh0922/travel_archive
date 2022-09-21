<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
session_start();
use Src\Photo;
use Src\Point;
$result = ["result" => 0];

try{
	if(!array_key_exists("login", $_SESSION) || $_SESSION["login"] == null){
		throw new Exception("session expire");
	}

	if(array_key_exists("bin", $_POST)){
		$photo = new Photo;
		$point = new Point;
		$img_name = $_POST["point"]."_".$_POST["name"];
		$photo->putFile($img_name, $_POST["bin"]);
		$exist_pin = $point->checkExist($_POST["point"]);
		if($exist_pin == false){
			$point_id = $point->savePoint($_POST["point"], (double)$_POST["lat"], (double)$_POST["lng"]);
		}else{
			$point_id = $exist_pin->pin_id;
		}
		if($point_id > 0){
			$point->saveImgPath($point_id, $img_name);
		}
	}
	$result["result"] = 1;
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>
