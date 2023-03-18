<?php
require(dirname(__FILE__)."/vendor/autoload.php");
session_start();
$login = false;
$limit = false;


if($_SESSION["login"] == "on"){
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

<?php if(!$limit){ ?>
  <script src="https://maps.google.com/maps/api/js?key=<?=$_ENV["G_MAP_API"] ?>&language=ja"></script>
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
        <ul class="menu-list">
          <li class="">
            <a href="#" class="dropdown-item">範囲</a>          
            <ul class="dropdown-list">
              <?php foreach($range_list as $key => $range_obj): ?>
              <li  >
                  <a href="#" 
                  class="dropdown-item" 
                  onClick="showCircle(<?php echo $range_obj["map_range"] ?>, <?php echo $range_obj["g_map_zoom"] ?>)"><?php echo $range_obj["range_label"] ?></a>
                </li>
              <?php endforeach ?>
            </ul>
          </li>
        </ul>

        <ul class="menu-list">
          <li class="menu-list">
            <a href="#" class="dropdown-item">フライト</a>
            <ul class="dropdown-list">
              
              <?php foreach ($depature_list as $key => $departure_obj): ?>
                <li class="dropdown-item">
                  <a href="#" class="dropdown-item" onClick="drawLine(<?php echo $departure_obj["pin_id"] ?>)"><?php echo $departure_obj["station_name"] ?></a>
                </li>    
              <?php endforeach ?> 
                <li class="dropdown-item">
                  <a href="#" class="dropdown-item" onclick="drawLine(0)">解除</a>
                </li>
            </ul>
          </li>
        </ul>        
      </div>


      <div class="cp-iptxt">
        <input type="text" id="st-name" value="" placeholder="駅名を入力" onCHange="search(event)"/>
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

    <script src="./dist/map.js"></script>
  <?php }else{  ?>
    <h1>閉鎖中</h1>
  <?php } ?>

</body>
</html>
