<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Photo;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0];

try {
	// Input validation
	if (!isset($_POST["pin_id"]) || !isset($_POST["category"])) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "pin_id and category parameters are required";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Validate and sanitize pin_id
	$id = filter_var($_POST["pin_id"], FILTER_VALIDATE_INT);
	if ($id === false || $id < 0) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "Invalid pin_id";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Validate and sanitize category
	$category = filter_var($_POST["category"], FILTER_VALIDATE_INT);
	if ($category === false || $category < 0) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "Invalid category";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	$photo = new Photo;
	$result["result"] = 1;
	$html = "";
	$img_list = $photo->getAllPhoto($id, $category);
	
	foreach ($img_list as $img) {
		// Sanitize filename to prevent XSS
		$safe_filename = htmlspecialchars($img["file_name"], ENT_QUOTES, 'UTF-8');
		$html .= sprintf("<img class='pin-photo' src='./img/%s' />", $safe_filename);
	}
	
	$result["html"] = $html;
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("getAllPinImg.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
