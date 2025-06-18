<?php
require(dirname(__FILE__)."/../vendor/autoload.php");

// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

use Src\Access;

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

	$access = new Access();
	$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
	$env->load();

	$today = date("Y-m-d");
	$current_access = $access->getCurrentCount($today);
	
	if ($current_access && $current_access->access_count >= $_ENV["G_ACCESS_LIMIT"]) {
		http_response_code(429);
		$result["result"] = -1;
		$result["message"] = "API access limit exceeded";
	} else {
		$access->addAccess($today);
		$result["result"] = 1;
	}
	
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Internal server error";
	error_log("cntGoogleAccess.php Error: " . $e->getMessage());
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
