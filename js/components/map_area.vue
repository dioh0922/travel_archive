<template>
	<div class="form-area" >
		<div class="edit-form">
			<div>
				<input type="text" id="st-name" v-bind:value="search" v-on:change="inputSearch"/>
				<input type="button" value="検索" placeholder="駅名を入力" v-on:click="searchStation"/>
			</div>
			<div>
				<select v-bind:value="select" v-on:change="categorySelect">
					<option v-for="item in category" v-bind:value="item.category_id">{{item.category_title}}</option>
				</select>
			</div>
		</div>
		<div id="map" class="map"></div>
	</div>
</template>

<script>
	export default {
		props:["category"],
		data(){
			return {
				select: 1,
				search: ""
			};
		},
		methods:{
			inputSearch(e){
				this.search = e.target.value;
			},
			categorySelect(e){
				this.select = e.target.value;
				this.initPin();
			},
			searchStation(){
				if(this.search != ""){
					this.$emit("search", {station: this.search});
				}else{
					openDialog("駅名を選択する");
				}
			},
			initPin(){
				this.$emit("init", {category: this.select});
			}
		},
		mounted(){
			let Options = {
				zoom: 8, /* 地図の縮尺値 */
				center: new window.google.maps.LatLng(35.681391, 139.766103), /* 地図の中心座標 */
				mapTypeId: "roadmap" /* 地図の種類 */
			};
			const map = new window.google.maps.Map(document.getElementById('map'), Options);
			window.map = map;
			this.initPin();
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
