<?php
require(dirname(__FILE__)."/vendor/autoload.php");
session_start();
$login = false;
if($_SESSION["login"] == "on"){
	$login = true;
}

$category = new Src\Category();
$category_list = $category->getCategory();

$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../env");
$env->load();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta charset="UTF-8">
<title>旅行記録</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<script src="http://maps.google.com/maps/api/js?key=<?=$_ENV["G_MAP_API"] ?>&language=ja"></script>
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

	HeartRails Express

	<div>
		<div class="cp-iptxt">
			<input type="text" id="st-name" value="" placeholder="駅名を入力" onCHange="search(event)"/>
			<i class="material-icons">location_on</i>
		</div>
		<div class="tab-wrap">
			<?php foreach ($category_list as $key => $category_obj) { ?>
				<?php if($key == 0){ ?>
					<input type="radio" checked id="tab<?php echo $category_obj["category_id"]; ?>" name="category" class="tab-switch" onChange="categorySelect(event)" value="<?php echo $category_obj["category_id"]; ?>"/>
					<label class="tab-label" for="tab<?php echo $category_obj["category_id"]; ?>"><?php echo $category_obj["category_title"]; ?></label>
				<?php }else{ ?>
					<input type="radio" id="tab<?php echo $category_obj["category_id"]; ?>" name="category" class="tab-switch" onChange="categorySelect(event)" value="<?php echo $category_obj["category_id"]; ?>"/>
					<label class="tab-label" for="tab<?php echo $category_obj["category_id"]; ?>"><?php echo $category_obj["category_title"]; ?></label>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

	<div id="map"></div>

	<div>
		<dialog class="dialog" id="img-dialog">
			<div id="img-preview"></div>
			<div>
				<input type="button" value="閉じる" onClick="closeDialog()" data-backdrop="true"/>
			</div>
		</dialog>
		<div id="dialog-background"></div>
	</div>

	<script src="./dist/map.js"></script>

</body>
</html>
