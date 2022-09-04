let load_img = {
	type: "",
	name: "",
	bin: ""
};
const upload_form = `
	<input type="file" name="img" onchange="loadImg(event)"/>
	<input type="button" value="追加" onclick="saveImg()"/>
`;

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
	console.log("test");
	let post_data = new FormData();
	post_data.append("type", load_img.type);
	post_data.append("name", load_img.name);
	post_data.append("bin", load_img.bin);
	axios.post("./api/addImg.php", post_data).then(res => {
		console.log("done");
	}).catch(er => {
		console.log(er);
	});
}

function initExistPin(){
	let marker = [];
	arr = [
		{
			lat: 35.681391,
			lng: 139.766103,
			html: "test"
		}
	];
	arr.forEach((item, i) => {
		let tmp = new google.maps.Marker({
			position: new google.maps.LatLng(item.lat, item.lng),
			map: map
		});
		marker.push(tmp);
		markerInfo(tmp, item.html);
	});
}

function markerInfo(marker, html){
	let info_wnd = new google.maps.InfoWindow({
		content: html,
		maxWIdth: 200
	});

	google.maps.event.addListener(marker, "click", function(event){
		info_wnd.open(map, marker);
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
				content: upload_form,
				maxWIdth: 200
			});
			google.maps.event.addListener(mark, "click", function(event){
				info_wnd.open(map, mark);
			});


		}
	}).catch(er => {
		console.log(er);
	});
}
window.search = search;
