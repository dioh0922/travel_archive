<?php
require(dirname(__FILE__)."/../vendor/autoload.php");

// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

use Src\Photo;

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

try {
	$photo = new Photo();
	$zipPath = $photo->dumpAllPhoto(); // Get ZIP path

	// Send ZIP response
	$zipName = 'travel_photos_' . date('Y-m-d_H-i-s') . '.zip';
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="' . $zipName . '"');
	header('Content-Length: ' . filesize($zipPath));
	readfile($zipPath);
	unlink($zipPath); // Clean up
	exit;
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Download failed: " . $e->getMessage();
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>