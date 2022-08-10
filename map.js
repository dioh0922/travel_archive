
let map;
let arr = [];

var MyLatLng = new google.maps.LatLng(35.6811673, 139.7670516);
var Options = {
	zoom: 8, /* 地図の縮尺値 */
	center: new google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
	mapTypeId: "roadmap" /* 地図の種類 */
};

(window.onload = () => {
	map = new google.maps.Map(document.getElementById('map'), Options);
});

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

			let add_img_form = '<input type="file"/><input type="button" value="追加"/>';
			let info_wnd = new google.maps.InfoWindow({
				content: add_img_form,
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
