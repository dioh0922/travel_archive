import axios from "axios";
import {mountDialog, unmountDialog} from "./util_module.js";

function createDump(){
  axios.get("./api/dumpImgList.php").then(res => {
    if(res.data.result == 0){
      openDialog("ダンプの生成を開始しました。");
    }else{
      throw new Error(res.data.message);
    }
  }).catch(er => {
    openDialog(er.message);
  });
}
window.createDump = createDump;

/**
 * 
 * */
function openDialog(str){
  console.log(str);
  mountDialog(str);
}
window.openDialog = openDialog;

/**
 * ダイアログの非表示
 * */
function closeDialog(){
  unmountDialog();
}
window.closeDialog = closeDialog;