import axios from "axios";
import {PinForm} from "./form_module.js";
//const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

const DEFAULT_ZOOM = 8; /* google map の初期ズーム */

let app = null;
let circle = null;
let open_wnd = null;
let arr = [];
const marker = [];
const defaultLat = 35.681236;
const defaultLng = 139.767125;
const layerGourp = [];

const map = L.map('map').setView([defaultLat, defaultLng], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
//var marker = L.marker([defaultLat, defaultLng]).addTo(map);
function onMapClick(e) {
  console.log(e);
  console.log("You clicked the map at " + e.latlng);
  //console.log(marker);
  //marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();

}

map.on('click', onMapClick);


/**
 * 送信する画像読み込み用オブジェクト
 * */
let load_img = {
  type: "",
  name: "",
  bin: "",
  category: {
    id: 0,
    zoom_level: DEFAULT_ZOOM
  },
  orientation: 0  /* 画像の読み込み方向 スマホ系は画素でなく、ここで表示を決めている */
};

/**
 *  写真追加用のピン管理オブジェクト
 * */
let open_pin = {
  name: "",
  lat: 0,  //経度
  lng: 0   //緯度
};

function initMap(){
  console.log("init map");
  
  /*
  let Options = {
    zoom: 8, 
    center: new google.maps.LatLng(35.681391, 139.766103),
    mapTypeId: "roadmap"
  };
  */
  //map = new window.google.maps.Map(document.getElementById('map'), Options);
  

}

(window.onload = () => {

  //initExistPin(-1, DEFAULT_ZOOM);

});


/**
 * 
 * */
function openDialog(str){
  document.getElementById("img-preview").innerHTML = str;
  document.getElementById("img-dialog").show();
  document.getElementById("dialog-background").style.display = "block";
}
window.openDialog = openDialog;

/**
 * ダイアログの非表示
 * */
function closeDialog(){
  document.getElementById("img-dialog").close();
  document.getElementById("dialog-background").style.display = "none";
}
window.closeDialog = closeDialog;

/**
 * ローディングアニメーション開始
 * */
function openLoading(){
  document.getElementById("loading-spinner").style.display = "block";
}
window.openLoading = openLoading;

/**
 * ローディングアニメーション終了
 * */
function closeLoading(){
  document.getElementById("loading-spinner").style.display = "none";
}
window.closeLoading = closeLoading;


/**
 * ピンの取得カテゴリ設定処理
 * e: イベント
 * zoom: mapのズームレベル
 * */
function categorySelect(e, zoom){
  document.getElementById("st-name").value = "";
  initExistPin(e.target.value, zoom);
}
window.categorySelect = categorySelect;


/**
 * 指定したカテゴリのピン配列を取得する処理
 * category: カテゴリ
 * zoom: mapのズームレベル
 * */
function initExistPin(category, zoom){
  removeAllLayer();

  load_img.category.id = category;
  load_img.category.zoom_level = zoom;
  let post_data = new FormData();
  post_data.append("category", category);
  openLoading();
  axios.post("./api/getExistRecord.php", post_data).then(res => {
    closeLoading();
    if(res.data.result == 1){
      console.log(res.data);
      map.setZoom(zoom);
      if(marker.length > 0){
        marker.forEach((mark, i) => {
          mark.pin.setMap(null);
        });
        marker.length = 0;
      }
      arr = res.data.list;
      arr.forEach((item, i) => {
        let pin = L.marker([item.lat, item.lng], {
          title: item.station_name
        }).addTo(map);
        pin.on('click', function() {
          const title = this.options.title;  // タイトルを取得
          const exist_form = PinForm.loadPinForm(item.station_name, item.pin_id);
          this.bindPopup(exist_form).openPopup();  // ポップアップにフォームを表示
        });
      });
    }else{
    }
  }).catch(er => {
    openDialog(er.message);
  });
}

/**
 * 地図に描画されているものを除去する
 */
function removeAllLayer(){
  map.eachLayer(function(layer) {
    if (layer instanceof L.TileLayer) {
      return;  // タイルレイヤーは削除しない
    }
    map.removeLayer(layer);  // すべてのレイヤーを削除
  });
}

/**
 * 入力した地名の位置情報取得処理
 * e: 検索文字列のイベント
 * */
function search(e){
  axios.get("./api/cntGoogleAccess.php").then(res => {
    if(res.data.result == 1){
      let target_position = e.target.value;

      searchGeocode(target_position).then(res => {
        map.setCenter(res);
        let target_location = res;
        if(marker.find(item => item.name == target_position) == void 0){
          /*
          let mark = new google.maps.Marker({
            map: map,
            title: target_position,
            position: target_location
          });

          let info_wnd = new google.maps.InfoWindow({
            content: PinForm.loadNewPinForm(target_position),
            maxWIdth: 200
          });
          google.maps.event.addListener(mark, "click", function(event){
            open_pin.name = target_position;
            open_pin.lat = target_location.lat(); //経度
            open_pin.lng = target_location.lng(); //緯度
            info_wnd.open(window.map, mark);
            open_wnd = info_wnd;
          });
          marker.push({pin: mark, name: target_position});
          */
        }

      });
    }else{
      throw new Error(res.data.message);
    }
  }).catch(er => {
    openDialog(er.message);
  });
}
window.search = search;

/**
 * GoogleAPIの位置情報取得処理
 * e: 検索文字列
 * */
async function searchGeocode(e){
  // TODO: 位置情報→geminiとかでできる？

  /*
  let geocoder = new google.maps.Geocoder();
  let target_position = e;

  let result = null;
  await geocoder.geocode({address: target_position, region: "jp"}, function(res, sts){
    if(sts == google.maps.GeocoderStatus.OK){
      result = res[0].geometry.location;
    }
  });
  */

  //return result;
}
window.searchGeocode = searchGeocode;

/**
 * 画像読み込み処理
 * e: 画像選択イベント
 * */
function loadImg(e){

  /* exifのライブラリは読み込みまで採りにいかない */
  import("exif-js").then(res => {
    const EXIF = res.default.EXIF;

    let evt = e.target.files[0];
    let exif = EXIF.getData(evt, function(){
      let result = EXIF.getTag(this, "Orientation");
      if(result != void 0){
        load_img.orientation = result;
      }
    });
    let reader = new FileReader();
    reader.onload = () => {
      let result = reader.result.split(",");
      if(result[1].length > 7500000){
        openDialog("ファイルが大きすぎます");
        return;
      }
      load_img.name = evt.name;
      load_img.type = evt.type;
      load_img.bin = result[1];
      document.getElementById("file-preview").src = reader.result;
      document.getElementById("file-preview").style.display = "block";
    }
    reader.readAsDataURL(evt);
  });
}
window.loadImg = loadImg;

/**
 * 画像保存処理
 * グローバル変数に各種データは持たせる
 * */
function saveImg(){
  let post_data = new FormData();
  post_data.append("type", load_img.type);
  post_data.append("name", load_img.name);
  post_data.append("bin", load_img.bin);
  post_data.append("category", load_img.category.id);
  post_data.append("orientation", load_img.orientation);
  post_data.append("lat", open_pin.lat); //lat:経度
  post_data.append("lng", open_pin.lng); //lng:緯度
  post_data.append("point", open_pin.name);
  openLoading();
  axios.post("./api/addImg.php", post_data).then(res => {
    closeLoading();
    if(res.data.result == 1){
      open_wnd.close();
      initExistPin(load_img.category.id, load_img.category.zoom_level);
    }else{
      throw new Error(res.data.message);
    }
  }).catch(er => {
    openDialog(er.message);
  });
}
window.saveImg = saveImg;

/**
 * 画像プレビューダイアログ表示処理
 * id: ピンのID
 * */
function openImgDialog(id){
  let post_data = new FormData();
  post_data.append("pin_id", id);
  post_data.append("category", load_img.category.id);
  openLoading();
  axios.post("./api/getAllPinImg.php", post_data).then(res => {
    closeLoading();
    if(res.data.result == 1){
      document.getElementById("img-preview").innerHTML = res.data.html;
      document.getElementById("img-dialog").show();
      document.getElementById("dialog-background").style.display = "block";
    }else{

    }
  }).catch(er => {

  });
}
window.openImgDialog = openImgDialog;

/**
 * ピンの情報ダイアログ表示処理
 * marker: ピンのオブジェクト
 * html: ダイアログ内容
 * name: ピンの名称
 * */
function markerInfo(marker, html, name){
  /*
  let info_wnd = new google.maps.InfoWindow({
    content: html,
    maxWIdth: 200
  });

  google.maps.event.addListener(marker, "click", function(event){
    info_wnd.open(map, marker);
    open_wnd = info_wnd;
    open_pin.name = name;
  });
  */
}

/**
 * ログイン処理
 * */
function login(){
  let post_data = new FormData();
  post_data.append("pass", document.getElementById("pass").value);
  openLoading();
  axios.post("../util_api/login.php", post_data).then(res => {
    closeLoading();
    if(res.data.result == 1){
      location.reload();
    }else{
      throw new Error("ログインに失敗しました");
    }
  }).catch(er => {
    openDialog(er.message);
  });
}
window.login = login;

/**
 * 限界範囲の表示処理
 * range: google mapに表示する円の半径
 * zoom: mapのズームレベル
 * */
function showCircle(range, zoom){
  if(circle != null){
    circle.setVisible(false);//非表示にする
    circle = null;
  }

  map.setZoom(zoom);

  if(range > 0){
    //起点は東京固定で良い?
    /*
    circle = new google.maps.Circle({
      center: new google.maps.LatLng(35.681391, 139.766103),
      fillColor: '#FF0000',
      fillOpacity: 0.2, //塗りつぶし透明度
      map: map,
      radius: range,  //半径
      strokeColor: '#FF0000',
      strokeOpacity: 1,
      strokeWeight: 1
    });
    */
  }
}
window.showCircle = showCircle;

let flight = {
  /*
  polyline: new google.maps.Polyline({
    geodesic: true,
    strokeColor: "#FF0000",
    strokeOpacity: 1.0,
    strokeWeight: 2,
  }),
  pin: null
  */
}

/**
 * 直線表示処理
 * flg: 表示モード
 * */
function drawFlight(id){
  map.setZoom(5);
  if(flight.pin != null){
    flight.pin.setMap(null); 
  }
  if(id > 0){
    let post_data = new FormData();
    post_data.append("departure_id", id);
    
    axios.post("./api/getFlightRoute.php", post_data).then(res => {
      let airport_list = [];
      let tmp = res.data.list;
      /*
      let departure_latlng = new google.maps.LatLng(res.data.departure.lat, res.data.departure.lng)
      let mark = new google.maps.Marker({
        map: map,
        title: res.data.departure.name,
        position: departure_latlng
      });
      flight.pin = mark;

      tmp.forEach(el => {
        airport_list.push(new google.maps.LatLng(el.lat, el.lng));
        airport_list.push(departure_latlng);
      });

      flight.polyline.setPath(airport_list);
      flight.polyline.setMap(map);
      flight.polyline.setVisible(true);
      */
    }).catch(er => {
      openDialog(er.toString());
    });
  }else{
    flight.polyline.setMap(null);
    flight.polyline.setVisible(false);
  }
}
window.drawFlight = drawFlight;

/**
 * 空港追加ダイアログ表示処理
 * */
function addAirport(){
  document.getElementById("add-airport-dialog").show();
  document.getElementById("dialog-background").style.display = "block";
}
window.addAirport = addAirport;

/**
 * 空港ダイアログ非表示処理
 * */
function closeAirportDlg(){
  document.getElementById("add-airport-dialog").close();
  document.getElementById("dialog-background").style.display = "none";
}
window.closeAirportDlg = closeAirportDlg;

/**
 * 空港情報追加処理
 * */
function addDestAirport(){
  let airport_name = document.getElementById("destination-name").value;
  let departure_id = parseInt(document.getElementById("departure-select").value);
  if(departure_id > 0){
    closeAirportDlg();
    searchGeocode(airport_name).then(res => {
      let post_data = new FormData();
      post_data.append("departure", departure_id);
      post_data.append("name", airport_name);
      post_data.append("lat", res.lat());
      post_data.append("lng", res.lng());
      openLoading(); 
      axios.post("./api/addFlightRoute.php", post_data).then(res => {
        closeLoading();
        if(res.data.result == -1){
          throw new Error(res.data.message);
        }
      }).catch(er => {
        openDialog(er.message);
      })
    });
  }else{
    focus(document.getElementById("departure-select"));
  }
}
window.addDestAirport = addDestAirport;