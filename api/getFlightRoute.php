<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Flight;
use Src\Point;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0, "list" => []];

try {
	// Input validation
	if (!isset($_POST["departure_id"])) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "departure_id parameter is required";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// Validate and sanitize departure_id
	$departure_id = filter_var($_POST["departure_id"], FILTER_VALIDATE_INT);
	if ($departure_id === false || $departure_id <= 0) {
		http_response_code(400);
		$result["result"] = -1;
		$result["message"] = "Invalid departure_id";
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	}

	$flight = new Flight();
	$point = new Point();
	
	$result["result"] = 1;
	$result["list"] = $flight->getAllRoute($departure_id);
	$result["departure"] = $point->getTargetPin($departure_id);
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("getFlightRoute.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
