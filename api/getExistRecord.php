<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Point;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0, "list" => []];

try {
	// Input validation for category
	if (!isset($_POST["category"])) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "Category parameter is required";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Validate and sanitize category input
	$category = filter_var($_POST["category"], FILTER_VALIDATE_INT);
	if ($category === false || $category < 0) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "Invalid category ID";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	$point = new Point;
	$result["result"] = 1;
	$result["list"] = $point->getAllPin($category);
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("getExistRecord.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
