<?php
require(dirname(__FILE__)."/../vendor/autoload.php");

// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

use Src\Flight;
use Src\Point;

// Set JSON response header
header('Content-Type: application/json');

$result = ["result" => 0];

try {
  // Enhanced session validation
  if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "on") {
    http_response_code(401);
    $result["result"] = -1;
    $result["message"] = "Authentication required";
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
  }

  // Check session expiry
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
  $required_fields = ['name', 'departure', 'lat', 'lng'];
  foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      http_response_code(400);
      $result["result"] = -1;
      $result["message"] = "Missing required field: " . $field;
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      exit;
    }
  }

  // Validate and sanitize inputs
  $destination_name = trim($_POST["name"]);
  if (strlen($destination_name) > 255) {
    throw new Exception("Destination name too long");
  }

  $departure_id = filter_var($_POST["departure"], FILTER_VALIDATE_INT);
  if ($departure_id === false || $departure_id <= 0) {
    throw new Exception("Invalid departure ID");
  }

  $lat = filter_var($_POST["lat"], FILTER_VALIDATE_FLOAT);
  if ($lat === false || $lat < -90 || $lat > 90) {
    throw new Exception("Invalid latitude");
  }

  $lng = filter_var($_POST["lng"], FILTER_VALIDATE_FLOAT);
  if ($lng === false || $lng < -180 || $lng > 180) {
    throw new Exception("Invalid longitude");
  }

  $point = new Point();
  $flight = new Flight();
  
  $exist_destination = $point->checkExist($destination_name);
  $destination_id = 0;
  
  if (!$exist_destination) {
    $destination_id = $point->savePoint($destination_name, $lat, $lng);
  } else {
    $destination_id = $exist_destination->pin_id;
  }

  if ($destination_id <= 0) {
    throw new Exception("Failed to create/retrieve destination point");
  }

  if (!$flight->chkExistRoute($departure_id, $destination_id)) {
    $result["route_id"] = $flight->addFlightRoute($departure_id, $destination_id);
    $result["result"] = 1;
  } else {
    http_response_code(409);
    $result["result"] = -1;
    $result["message"] = "Route already exists";
  }
  
} catch (Exception $e) {
  http_response_code(500);
  $result["result"] = -1;
  $result["message"] = "Internal server error";
  error_log("addFlightRoute.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
