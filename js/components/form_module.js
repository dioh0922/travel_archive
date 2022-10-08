/*
Goole map api 用のhtml関係は一旦モジュールに分ける
vueのライブラリ(vue3-google-maps)にはしない
*/

const upload_form = `
<b>@st_name@</b><br>
<input type="file" name="img" onchange="loadImg(event)"/><br>
<input type="button" value="追加" onclick="saveImg()"/><br>
`;

export class PinForm{
	static loadPinForm(name, id){
		let exist_form = upload_form.replaceAll("@st_name@", name);
		exist_form += '<input type="button" value="一覧" onClick="openImgDialog(@id@)"/>'.replaceAll("@id@", id);
		return exist_form;
	}
}
