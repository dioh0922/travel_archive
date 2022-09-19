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
.pin-photo{
	width: 10%;
	height: 20%;
}
#img-dialog{
	z-index:999;
	top:0;
}

dialog{
	position: absolute;
	padding:0;
	border:0;
	border-radius:0.6rem;
	box-shadow: 0 0 1em black;
}
dialog::backdrop {
  /* 背景を半透明のブラックにする */
  background-color: rgba(0, 0, 0, 0.4);
}
dialog + .backdrop {
	position:fixed;
  background-color: rgba(0, 0, 0, 0.4);
}
dialog[open] {
  animation: slide-up 0.4s ease-out;
}
</style>
</head>

<body>
	<input type="text" id="st-name" value=""/>
	<input type="button" value="検索" onClick="search()"/>
	<div id="map"></div>

	<dialog class="dialog" id="img-dialog">
		<div id="img-preview"></div>
		<div>
			<input type="button" value="閉じる" onClick="closeDialog()"/>
		</div>
	</dialog>

	<script src="./map.js"></script>
	HeartRails Express
</body>
</html>
