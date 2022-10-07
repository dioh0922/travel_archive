<template>
	<div class="form-area" >
		<div class="edit-form">
			<input type="text" id="st-name" value=""/>
			<input type="button" value="検索" placeholder="駅名を入力" onClick="search()"/>
			<select onChange="categorySelect(event)">
				<option v-for="item in category" v-bind:value="item.category_id">{{item.category_title}}</option>
				<!--
				<?php foreach ($category_list as $key => $category_obj) { ?>
					<option value="<?php echo $category_obj["category_id"] ?>"/><?php echo $category_obj["category_title"]; ?></>
				<?php } ?>
				-->
			</select>
		</div>
		<div id="map" class="map"></div>
	</div>
</template>

<script>
	export default {
		props:["category"],
		mounted(){
			let Options = {
				zoom: 8, /* 地図の縮尺値 */
				center: new window.google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
				mapTypeId: "roadmap" /* 地図の種類 */
			};
			const map = new window.google.maps.Map(document.getElementById('map'), Options);
			window.map = map;
			this.$emit("init");
		}
	}
</script>

<style>
#map{
	height: 80%;
	width: 100%;
	position: absolute;
}
.edit-form{
	display:block;
	height: 50px;
}
.form-area{
	width:100%;
}
</style>
