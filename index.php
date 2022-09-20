<?php
require(dirname(__FILE__)."/vendor/autoload.php");

$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../env");
$env->load();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta name=”viewport” content=”width=device-width,initial-scale=1″>
<meta charset="UTF-8">
<title>Sample_GoogleMap</title>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="http://maps.google.com/maps/api/js?key=<?=$_ENV["G_MAP_API"] ?>&language=ja"></script>
<link rel="stylesheet" href="./travel_archive.css">
</head>

<body>
	<input type="text" id="st-name" value=""/>
	<input type="button" value="検索" onClick="search()"/>
	<div id="map"></div>

	<dialog class="dialog" id="img-dialog">
		<div id="img-preview"></div>
		<div>
			<input type="button" value="閉じる" onClick="closeDialog()" data-backdrop="true"/>
		</div>
	</dialog>
	<div id="dialog-background"></div>

	<script src="./map.js"></script>
	HeartRails Express
</body>
</html>
