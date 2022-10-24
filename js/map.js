import axios from "axios";
import EXIF from "exif-js";
import {PinForm} from "./form_module.js";

let app = null;
let map = null;
let marker = [];

let load_img = {
	type: "",
	name: "",
	bin: "",
	category: 0,
	orientation: 0	/* 画像の読み込み方向 スマホ系は画素でなく、ここで表示を決めている */
};

let open_pin = {
	name: "",
	x: 0,
	y: 0
};

(window.onload = () => {

	let Options = {
		zoom: 8, /* 地図の縮尺値 */
		center: new google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
		mapTypeId: "roadmap" /* 地図の種類 */
	};
	map = new window.google.maps.Map(document.getElementById('map'), Options);
	initExistPin(-1);

	/*
	const form = createApp({
		data(){
			return{
				category:[]
			};
		},
		methods:{
			initAllCategory(){
				axios.get("./api/getAllCategory.php").then(res => {
					if(res.data.result == 1){
						this.category = res.data.list;
					}
				}).catch(er => {

				});
			},
		mounted(){
			this.initAllCategory();
		}
	});

	form.component("container", Container);
	form.mount("#app-container");
	*/
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


function categorySelect(e){
	document.getElementById("st-name").value = "";
	initExistPin(e.target.value);
}
window.categorySelect = categorySelect;

function initExistPin(category){
	load_img.category = category;
	let post_data = new FormData();
	post_data.append("category", category);
	axios.post("./api/getExistRecord.php", post_data).then(res => {
		if(res.data.result == 1){
			if(marker.length > 0){
				marker.forEach((mark, i) => {
					mark.pin.setMap(null);
				});
				marker.length = 0;
			}
			arr = res.data.list;
			arr.forEach((item, i) => {
				let tmp = new google.maps.Marker({
					position: new google.maps.LatLng(item.lng, item.lat),
					map: map
				});
				marker.push({pin: tmp, name: item.station_name});
				let exist_form = PinForm.loadPinForm(item.station_name, item.pin_id);
				markerInfo(tmp, exist_form, item.station_name);
			});
		}else{
		}
	}).catch(er => {
	});
}

function search(e){
	let post_data = new FormData();
	post_data.append("method", "getStations");
	post_data.append("name", e.target.value);
	axios.post("https://express.heartrails.com/api/json/", post_data).then(res => {
		if(res.data.response.station != void 0){
			let tmp = res.data.response.station[0];

			if(marker.find(item => item.name == tmp.name) == void 0){
				const latlng = new google.maps.LatLng(tmp.y, tmp.x);
				let mark = new google.maps.Marker({
					position: latlng,
					map: map,
					title: tmp.name
				});

				let info_wnd = new google.maps.InfoWindow({
					content: PinForm.loadNewPinForm(tmp.name),
					maxWIdth: 200
				});
				google.maps.event.addListener(mark, "click", function(event){
					open_pin.name = tmp.name;
					open_pin.y = tmp.y;
					open_pin.x = tmp.x;
					info_wnd.open(window.map, mark);
					open_wnd = info_wnd;
				});
				marker.push({pin: mark, name: tmp.name});
			}
		}
	}).catch(er => {
		console.log(er);
	});
}
window.search = search;


function loadImg(e){
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
	}
	reader.readAsDataURL(evt);
}
window.loadImg = loadImg;

function saveImg(){
	let post_data = new FormData();
	post_data.append("type", load_img.type);
	post_data.append("name", load_img.name);
	post_data.append("bin", load_img.bin);
	post_data.append("category", load_img.category);
	post_data.append("orientation", load_img.orientation);
	post_data.append("lat", open_pin.x);
	post_data.append("lng", open_pin.y);
	post_data.append("point", open_pin.name);
	axios.post("./api/addImg.php", post_data).then(res => {
		if(res.data.result == 1){
			open_wnd.close();
			initExistPin(load_img.category);
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
	post_data.append("category", load_img.category);
	axios.post("./api/getAllPinImg.php", post_data).then(res => {
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
	axios.post("../util_api/login.php", post_data).then(res => {
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
