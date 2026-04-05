<?php
require(dirname(__FILE__)."/../../vendor/autoload.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('memory_limit', '-1');
set_time_limit(0);

use Src\Photo;
use Src\Log;

$log = new Log();

try {
	$photo = new Photo();
	$zipPath = $photo->dumpAllPhoto(); // Get ZIP path

	// Define dump directory
	$dumpDir = dirname(__FILE__) . '/../../dump/';
	if (!is_dir($dumpDir)) {
		mkdir($dumpDir, 0755, true);
	}

	// Send ZIP response
	$zipName = 'travel_photos_' . date('Y-m-d_H-i-s') . '.zip';
	$dumpPath = $dumpDir . $zipName;

	// Copy ZIP to dump directory
	if (copy($zipPath, $dumpPath)) {
		$log->logInfo("ZIP file created and saved to dump directory: " . $dumpPath);
		// Clean up temporary file
		unlink($zipPath);

		// Delete ZIP files older than 90 days based on filename timestamp
		$files = glob($dumpDir . '*.zip');
		if ($files !== false) {
			$now = time();
			$maxAge = 90 * 24 * 60 * 60;
			foreach ($files as $file) {
				if (!is_file($file)) {
					continue;
				}
				$baseName = basename($file);
				if (preg_match('/^travel_photos_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.zip$/', $baseName, $matches)) {
					$timestamp = DateTime::createFromFormat('Y-m-d_H-i-s', $matches[1]);
					if ($timestamp !== false) {
						$fileTime = $timestamp->getTimestamp();
						if (($now - $fileTime) > $maxAge) {
							if (unlink($file)) {
								$log->logInfo("Deleted old dump ZIP: " . $file);
							} else {
								$log->logError("Failed to delete old dump ZIP: " . $file);
							}
						}
					}
				}
			}
		}
	} else {
		throw new Exception("Failed to copy ZIP file to dump directory");
	}

} catch (Exception $e) {
  $log->logError("createDumpImgList.php Error: " . $e->getMessage());
}
?>