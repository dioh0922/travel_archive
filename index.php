<?php
require(dirname(__FILE__)."/vendor/autoload.php");

$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../env");
$env->load();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>Sample_GoogleMap</title>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="http://maps.google.com/maps/api/js?key=<?=$_ENV["G_MAP_API"] ?>&language=ja"></script>

<style>
html { height: 100% }
body { height: 100% }
#map { height: 80%; width: 100%}
</style>
</head>

<body>
	<input type="text" id="st-name" value=""/>
	<input type="button" value="検索" onClick="search()"/>
<div id="map"></div>

<script src="./map.js"></script>
HeartRails Express
</body>
</html>
