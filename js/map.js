import axios from "axios";
import {PinForm} from "./form_module.js";

const DEFAULT_ZOOM = 8;

let app = null;
let map = null;
let marker = [];

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

let open_pin = {
  name: "",
  lat: 0,  //経度
  lng: 0   //緯度
};

(window.onload = () => {

  let Options = {
    zoom: 8, /* 地図の縮尺値 */
    center: new google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
    mapTypeId: "roadmap" /* 地図の種類 */
  };
  map = new window.google.maps.Map(document.getElementById('map'), Options);
  initExistPin(-1, DEFAULT_ZOOM);

});

let open_wnd = null;
let arr = [];

function openDialog(str){
  document.getElementById("img-preview").innerHTML = str;
  document.getElementById("img-dialog").show();
  document.getElementById("dialog-background").style.display = "block";
}
window.openDialog = openDialog;
function closeDialog(){
  document.getElementById("img-dialog").close();
  document.getElementById("dialog-background").style.display = "none";
}
window.closeDialog = closeDialog;
function closeLoading(){
  document.getElementById("loading-spinner").style.display = "none";
}
window.closeLoading = closeLoading;
function openLoading(){
  document.getElementById("loading-spinner").style.display = "block";
}
window.openLoading = openLoading;


function categorySelect(e, zoom){
  document.getElementById("st-name").value = "";
  initExistPin(e.target.value, zoom);
}
window.categorySelect = categorySelect;

function initExistPin(category, zoom){
  load_img.category.id = category;
  load_img.category.zoom_level = zoom;
  let post_data = new FormData();
  post_data.append("category", category);
  openLoading();
  axios.post("./api/getExistRecord.php", post_data).then(res => {
    closeLoading();
    if(res.data.result == 1){
      map.setZoom(zoom);
      if(marker.length > 0){
        marker.forEach((mark, i) => {
          mark.pin.setMap(null);
        });
        marker.length = 0;
      }
      arr = res.data.list;
      arr.forEach((item, i) => {
        let tmp = new google.maps.Marker({
          position: new google.maps.LatLng(item.lat, item.lng),
          map: map
        });
        marker.push({pin: tmp, name: item.station_name});
        let exist_form = PinForm.loadPinForm(item.station_name, item.pin_id);
        markerInfo(tmp, exist_form, item.station_name);
      });
    }else{
    }
  }).catch(er => {
    openDialog(er.message);
  });
}

function search(e){
  axios.get("./api/cntGoogleAccess.php").then(res => {
    if(res.data.result == 1){
      searchGeocode(e);
    }else{
      throw new Error(res.data.message);
    }
  }).catch(er => {
    openDialog(er.message);
  });
}
window.search = search;

function searchGeocode(e){
  let geocoder = new google.maps.Geocoder();
  let target_position = e.target.value;
  geocoder.geocode(
    {address: target_position, region: "jp"},
    function(res, sts){
      if(sts == google.maps.GeocoderStatus.OK){
        let target_location = res[0].geometry.location;
        map.setCenter(target_location);
        if(marker.find(item => item.name == target_position) == void 0){
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
        }

      }
    }
  );

}
window.searchGeocode = searchGeocode;


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

function markerInfo(marker, html, name){
  let info_wnd = new google.maps.InfoWindow({
    content: html,
    maxWIdth: 200
  });

  google.maps.event.addListener(marker, "click", function(event){
    info_wnd.open(map, marker);
    open_wnd = info_wnd;
    open_pin.name = name;
  });
}

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
