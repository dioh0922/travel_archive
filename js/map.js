import axios from "axios";
import {PinForm} from "./form_module.js";
//const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

const DEFAULT_ZOOM = 8; /* google map の初期ズーム */

let app = null;
let circle = null;
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
function onMapClick(e) {
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

(window.onload = () => {});


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
        const exist = createPin(item.lat, item.lng, item.station_name);
        exist.on('click', function() {
          open_pin = {name: item.station_name, lat:item.lat, lng: item.lng};
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

function createPin(lat, lng, title){
  let pin = L.marker([lat, lng], {
    title: title
  }).addTo(map);
  return pin;
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
      searchGeocode(e.target.value);
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
  const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(e)}&format=json`;
  // APIリクエスト
  axios.get(url)
  .then(response => {
    const pin = response.data.filter(el => el.addresstype === 'railway' || el.addresstype === 'historic' || el.addresstype == 'tourism');
    if(pin.length > 0){
      removeAllLayer();
      const new_pin = createPin(pin[0].lat, pin[0].lon, e);
      new_pin.on('click', function () {
        open_pin = {name: e, lat:pin[0].lat, lng: pin[0].lon};
        const form = PinForm.loadNewPinForm(e);
        this.bindPopup(form).openPopup();  // ポップアップにフォームを表示  
      });
      map.setView([pin[0].lat, pin[0].lon], 13);
    }
  }).catch(er => {
    openDialog(er);
  });
}
window.searchGeocode = searchGeocode;

/**
 * 画像読み込み処理
 * e: 画像選択イベント
 * */
function loadImg(e){
  console.log(e);
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
      map.closePopup();
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
