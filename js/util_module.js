/**
 * 
 * */
export function mountDialog(str){
  document.getElementById("img-preview").innerHTML = str;
  document.getElementById("img-dialog").show();
  document.getElementById("dialog-background").style.display = "block";
}

/**
 * ダイアログの非表示
 * */
export function unmountDialog(){
  document.getElementById("img-dialog").close();
  document.getElementById("dialog-background").style.display = "none";
}