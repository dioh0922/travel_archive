<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Photo;
$result = ["result" => 0, "list" => []];
try{
	$photo = new Photo;
	$id = 0;
	if(array_key_exists("pin_id", $_POST)){
		$id = (int)$_POST["pin_id"];
	}
	$result["result"] = 1;
	$html = "";
	$img_list = $photo->getAllPhoto($id);
	foreach($img_list as $img){
		$html .= sprintf("<img class='pin-photo' src='./img/%s' />", $img["file_name"]);
	}
	$result["html"] = $html;
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
