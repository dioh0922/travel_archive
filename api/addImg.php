<?php
require(dirname(__FILE__)."/../vendor/autoload.php");

// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

use Src\Photo;
use Src\Point;
use Src\Log;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0];
$log = new Log();

try {
	// Enhanced session validation
	if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "on") {
		http_response_code(401);
		$result["result"] = -1;
		$result["message"] = "Authentication required";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Check session expiry (30 minutes timeout)
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
		session_unset();
		session_destroy();
		http_response_code(401);
		$result["result"] = -1;
		$result["message"] = "Session expired";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}
	$_SESSION['last_activity'] = time();

	// Input validation
	$required_fields = ['bin', 'point', 'lat', 'lng', 'name', 'orientation', 'category'];
	foreach ($required_fields as $field) {
		if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
			http_response_code(400);
			$result["result"] = -1;
			$result["message"] = "Missing required field: " . $field;
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}

	// Validate and sanitize input data
	$point_name = trim($_POST["point"]);
	if (strlen($point_name) > 255) {
		throw new Exception("Point name too long");
	}

	$lat = filter_var($_POST["lat"], FILTER_VALIDATE_FLOAT);
	if ($lat === false || $lat < -90 || $lat > 90) {
		throw new Exception("Invalid latitude");
	}

	$lng = filter_var($_POST["lng"], FILTER_VALIDATE_FLOAT);
	if ($lng === false || $lng < -180 || $lng > 180) {
		throw new Exception("Invalid longitude");
	}

	$category = filter_var($_POST["category"], FILTER_VALIDATE_INT);
	if ($category === false || $category < 0) {
		throw new Exception("Invalid category");
	}

	$orientation = filter_var($_POST["orientation"], FILTER_VALIDATE_INT);
	if ($orientation === false || !in_array($orientation, [0, 1, 2, 3, 4, 5, 6, 7, 8])) {
		throw new Exception("Invalid orientation");
	}

	$image_name = trim($_POST["name"]);
	if (strlen($image_name) > 200) {
		throw new Exception("Image name too long");
	}

	$photo = new Photo;
	$point = new Point;
	
	$exist_pin = $point->checkExist($point_name);
	$point_id = 0;
	
	if ($exist_pin == false) {
		$point_id = $point->savePoint($point_name, $lat, $lng);
	} else {
		$point_id = $exist_pin->pin_id;
	}

	if ($point_id <= 0) {
		throw new Exception("Failed to create/retrieve point");
	}

	$img_name = $point_id . "_" . $image_name;
	$photo->putFile($img_name, $_POST["bin"], $orientation);
	$point->saveImgPath($point_id, $img_name, $category);
	
	$log->logInfo("Image saved successfully: " . $image_name . " for point: " . $point_name);
	$result["result"] = 1;
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Upload failed";
	$log->logError("addImg.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
