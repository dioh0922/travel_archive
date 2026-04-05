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
	$scriptPath = dirname(__FILE__) . '/bat/createDumpImgList.php';
	if (!file_exists($scriptPath)) {
		throw new Exception('Dump script not found');
	}

	// Linux only: use CLI PHP binary path
	$phpBinary = trim(shell_exec('command -v php')) ?: PHP_BINARY;
	if (!$phpBinary || !is_executable($phpBinary)) {
		throw new Exception('PHP CLI binary not available');
	}

	$command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($scriptPath) . ' > /dev/null 2>&1 &';
	exec($command, $output, $returnVar);
	if ($returnVar !== 0) {
		throw new Exception('Failed to start dump job: ' . implode(' ', $output));
	}

	$result["result"] = 0;
	$result["cmd"] = $command;
	$result["message"] = "Dump job started";
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	exit;
} catch (Exception $e) {
	http_response_code(500);
	$result["result"] = -1;
	$result["message"] = "Failed to start dump job: " . $e->getMessage();
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>