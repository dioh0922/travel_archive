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