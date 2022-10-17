<?php
require(dirname(__FILE__)."/vendor/autoload.php");
session_start();
$login = false;
if($_SESSION["login"] == "on"){
	$login = true;
}

$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../env");
$env->load();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta name=”viewport” content=”width=device-width,initial-scale=1″>
<meta charset="UTF-8">
<title>Sample_GoogleMap</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<script src="https://maps.google.com/maps/api/js?key=<?=$_ENV["G_MAP_API"] ?>&language=ja"></script>
<link rel="stylesheet" href="./travel_archive.css">
</head>

<body>
	<?php if(!$login){ ?>
		<div class="row">
			<input type="password" name="pass" id="pass" placeholder="パスワード" value="">
			<button onClick="login()">
				<i class="tiny material-icons">vpn_key</i>
			</button>
		</div>
	<?php } ?>

	<input type="text" id="st-name" value=""/>
	<input type="button" value="検索" placeholder="駅名を入力" onClick="search()"/>
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
