<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Category;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0, "list" => []];

try {
	$category = new Category();
	$result["result"] = 1;
	$result["list"] = $category->getCategory();
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("getAllCategory.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
