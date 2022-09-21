let load_img = {
	type: "",
	name: "",
	bin: ""
};

let open_pin = {
	name: "",
	x: 0,
	y: 0
};

const upload_form = `
	<b>@st_name@</b><br>
	<input type="file" name="img" onchange="loadImg(event)"/><br>
	<input type="button" value="追加" onclick="saveImg()"/><br>
`;

let open_wnd = null;
let map;
let arr = [];

let MyLatLng = new google.maps.LatLng(35.6811673, 139.7670516);
let Options = {
	zoom: 8, /* 地図の縮尺値 */
	center: new google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
	mapTypeId: "roadmap" /* 地図の種類 */
};

(window.onload = () => {
	map = new google.maps.Map(document.getElementById('map'), Options);
	initExistPin();
});

function openDialog(str){
	document.getElementById("img-preview").innerHTML = str;
	document.getElementById("img-dialog").show();
	document.getElementById("dialog-background").style.display = "block";
}
function closeDialog(){
	document.getElementById("img-dialog").close();
	document.getElementById("dialog-background").style.display = "none";
}


function loadImg(e){
	let evt = e.target.files[0];
	let reader = new FileReader();
	reader.onload = () => {
		let result = reader.result.split(",");
		load_img.name = evt.name;
		load_img.type = evt.type;
		load_img.bin = result[1];
	}
	reader.readAsDataURL(evt);
}

function saveImg(){
	let post_data = new FormData();
	post_data.append("type", load_img.type);
	post_data.append("name", load_img.name);
	post_data.append("bin", load_img.bin);
	post_data.append("lat", open_pin.x);
	post_data.append("lng", open_pin.y);
	post_data.append("point", open_pin.name);
	axios.post("./api/addImg.php", post_data).then(res => {
		if(res.data.result == 1){
			open_wnd.close();
		}else{
			throw new Error(res.data.message);
		}
	}).catch(er => {
		openDialog(er.message);
	});
}

function initExistPin(){
	let marker = [];
	axios.get("./api/getExistRecord.php").then(res => {
		if(res.data.result == 1){
			arr = res.data.list;
			arr.forEach((item, i) => {
				let tmp = new google.maps.Marker({
					position: new google.maps.LatLng(item.lng, item.lat),
					map: map
				});
				marker.push(tmp);
				let exist_form = upload_form.replaceAll("@st_name@", item.station_name);
				exist_form += '<input type="button" value="一覧" onClick="openImgDialog(@id@)"/>'.replaceAll("@id@", item.pin_id)
				markerInfo(tmp, exist_form, item.station_name);
			});
		}else{

		}
	}).catch(er => {

	});
}

function openImgDialog(id){
	let post_data = new FormData();
	post_data.append("pin_id", id);
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

function search(){
	let post_data = new FormData();
	post_data.append("method", "getStations");
	post_data.append("name", document.getElementById("st-name").value);
	axios.post("http://express.heartrails.com/api/json/", post_data).then(res => {
		if(res.data.response.station != void 0){
			let tmp = res.data.response.station[0];

			const latlng = new google.maps.LatLng(tmp.y, tmp.x);
	    let mark = new google.maps.Marker({
	      position: latlng,
	      map: map,
				title: tmp.name
	    });


			let info_wnd = new google.maps.InfoWindow({
				content: upload_form.replaceAll("@st_name@", tmp.name),
				maxWIdth: 200
			});
			google.maps.event.addListener(mark, "click", function(event){
				open_pin.name = tmp.name;
				open_pin.y = tmp.y;
				open_pin.x = tmp.x;
				info_wnd.open(map, mark);
				open_wnd = info_wnd;
			});

		}
	}).catch(er => {
		console.log(er);
	});
}
window.search = search;



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
