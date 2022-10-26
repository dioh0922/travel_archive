/*
Goole map api 用のhtml関係は一旦モジュールに分ける
vueのライブラリ(vue3-google-maps)にはしない
*/

const upload_form = `
<b>@st_name@</b><br>
<button class="icon-btn add-btn">
	<div class="add-icon"></div>
	<label for="upload-file" class="btn-txt">写真の追加</label>
</button>
<div id="load-status"></div>
<input type="file" id="upload-file" class="upload-file" name="img" onchange="loadImg(event)"/><br>
<input type="button" class="upload-form upload-btn" value="追加" onclick="saveImg()"/><br>
`;

const btn_template = `
<button class="upload-form img-disp-btn" onClick="openImgDialog(@id@)">
	一覧
</button>
`;
export class PinForm{
	static loadPinForm(name, id){
		let exist_form = upload_form.replaceAll("@st_name@", name);
		exist_form += btn_template.replaceAll("@id@", id);
		return exist_form;
	}
	static loadNewPinForm(name){
		return upload_form.replaceAll("@st_name@", name);
	}
}
