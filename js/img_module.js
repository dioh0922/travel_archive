
export function compressFile(file){
  return new Promise((resolve, reject) => {
    let result = file.split(",");
    if(result[1].length > 7500000){
      const img = new Image();
      img.onload = function() {
          const canvas = document.getElementById('canvas');
          const ctx = canvas.getContext('2d');
    
          // 画像の新しいサイズ (例えば、元の画像の半分に縮小)
          const width = img.width / 2;
          const height = img.height / 2;
    
          // Canvasに画像を描画
          canvas.width = width;
          canvas.height = height;
          ctx.drawImage(img, 0, 0, width, height);
    
          // Base64に変換して表示する
          const resizedBase64 = canvas.toDataURL('image/jpeg', 0.7); // JPEG形式に変換、品質を70%に
          resolve(resizedBase64);
      };
      img.src = file;  
    }else{
      resolve(file);
    }
  });
}