<?php
require(dirname(__FILE__)."/vendor/autoload.php");
session_start();
$login = false;
$limit = false;


if(array_key_exists("login", $_SESSION)
  && $_SESSION["login"] == "on"){
  $login = true;
}
$access = new Src\Access();
$today = date("Y-m-d");
$current_access = $access->getCurrentCount($today);
if($current_access != null){
  if($current_access->access_count >= $_ENV["G_ACCESS_LIMIT"]){
    $limit = true;
  }else{
    $access->addAccess($today);
  }
}else{
  $access->saveAccess($today);
}

/*
アクセスのカウント
*/
$category_list = [];
$range_list = [];
$depature_list = [];
if(!$limit){
  $category = new Src\Category();
  $category_list = $category->getCategory();
  $range = new Src\Range();
  $range_list = $range->getRange();
  $point = new Src\Point();
  $depature_list = $point->getAllDeparture();
}


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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""></script>
<?php if(!$limit){ ?>
<?php } ?>
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

  <?php if(!$limit){ ?>
    <div class="container">
      <div class="menu-wrap">
      </div>


      <div class="cp-iptxt">
        <input type="text" id="st-name" value="" placeholder="駅名を入力" onChange="search(event)"/>
        <i class="material-icons">location_on</i>
      </div>

      <div class="tab-wrap ">
        <?php foreach ($category_list as $key => $category_obj) { ?>
          <input type="radio" id="tab<?php echo $category_obj["category_id"]; ?>" name="category" class="tab-switch" onChange="categorySelect(event, <?php echo $category_obj['map_zoom_level'] ?>)" value="<?php echo $category_obj["category_id"]; ?>"/>
          <label class="tab-label" for="tab<?php echo $category_obj["category_id"]; ?>"><?php echo $category_obj["category_title"]; ?></label>
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

    <div id="loading-spinner">
      <div class="spinner">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
        <div class="rect4"></div>
        <div class="rect5"></div>
      </div>
    </div>

    <canvas id="canvas"></canvas>

    <script src="./dist/map.js"></script>
  <?php }else{  ?>
    <h1>閉鎖中</h1>
  <?php } ?>

</body>
</html>
