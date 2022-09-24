<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Category;
$result = ["result" => 0, "list" => []];
try{
	$category = new Category();
	$result["result"] = 1;
	$result["list"] = $category->getCategory();
}catch(Exception $e){
	$result["result"] = -1;
	$result["message"] = $e->getMessage();
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
