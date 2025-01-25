/*
Goole map api 用のhtml関係は一旦モジュールに分ける
vueのライブラリ(vue3-google-maps)にはしない
*/

const upload_form = `
<b>@st_name@</b><br>
<button class="upload-form img-disp-btn" onclick="document.getElementById('upload-file').click();">
  写真
</button>
<input type="file" id="upload-file" class="upload-file" name="img" onchange="loadImg(event)" style="display: none;" />
<img id="file-preview" />
<br/>
<input type="button" class="upload-form upload-btn" value="登録" onclick="saveImg()"/><br>
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
