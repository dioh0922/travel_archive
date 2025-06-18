<?php
namespace Src;
use ORM;
use Dotenv;
use Exception;

class Photo{
	private const IMG_DIR = "/../img/";
	private const RESIZE_RATE = 0.5;

	private const MODE_90_DEGREE = 6;
	private const MODE_180_DEGREE = 3;
	private const MODE_270_DEGREE = 8;
	/*
	exifのOrientationに対する対応一覧
	 ←x→	の画像の時
	↑
	y
	↓

	1	そのまま 													 (y[0]が上 x[0]が左)
	2	水平方向反転(右が左) 							 (y[0]が上 x[0]が右)
	3	180度回転 									 			  (y[0]が下 x[0]が右)
	4	垂直方向反転(上が下に) 	 						(y[0]が下 x[0]が左)
	5	水平方向反転、時計周りに270度回転		(y[0]が左 x[0]が上)
	6	時計周りに90度回転									(y[0]が右 x[0]が上)
	7	水平方向反転、時計周りに90度回転			(y[0]が右 x[0]が下)
	8	時計周りに270度回転									(y[0]が左 x[0]が下)
	*/

	public function __construct(){
		$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
		$env->load();
		ORM::configure("mysql:host=".$_ENV["DB_HOST"].";port=".$_ENV["DB_PORT"]."charset=utf8;dbname=".$_ENV["DB_DB"]);
		ORM::configure("username", $_ENV["DB_USER"]);
		ORM::configure("password", $_ENV["DB_PASS"]);
	}
	public function putFile(string $name, string $bin, int $orientation){
		if(!$this->checkValidFileName($name)){
			throw new Exception("invalid file ext");
		}
		if(!$this->checkValidFileSize($bin)){
			throw new Exception("invalid file size");
		}
		$data = base64_decode($bin);
		$image = imagecreatefromstring($data);
		$file_info = getimagesizefromstring($data);
		if($image === false || $file_info === false){
			throw new Exception("invalid image bin");
		}
		if(!$this->checkValidMimeType($file_info["mime"])){
			throw new Exception("invalid mime type");
		}
		$src_width = $file_info[0];
		$src_height = $file_info[1];

		if($orientation == self::MODE_90_DEGREE
		|| $orientation == self::MODE_270_DEGREE){
			$src_width = $file_info[1];
			$src_height = $file_info[0];
		}

		switch($orientation){
			case self::MODE_90_DEGREE:
				$image = imagerotate($image, 270, 0);
				break;
			case self::MODE_270_DEGREE:
				$image = imagerotate($image, 90, 0);
				break;
			case self::MODE_180_DEGREE:
				$image = imagerotate($image, 180, 0);
			default:
				break;
		}
		$canvas = imagecreatetruecolor($src_width * self::RESIZE_RATE, $src_height * self::RESIZE_RATE);

		imagecopyresampled(
			$canvas,
			$image,
			0,
			0,
			0,
			0,
			$src_width * self::RESIZE_RATE,
			$src_height * self::RESIZE_RATE,
			$src_width,
			$src_height
		);

		switch($file_info[2]){
			case IMAGETYPE_JPEG:
				if(!imagejpeg($canvas, dirname(__FILE__).self::IMG_DIR.$name)){
					throw new Exception("err save jpg");
				}
				break;
			case IMAGETYPE_PNG:
				if(!imagepng($canvas, dirname(__FILE__).self::IMG_DIR.$name)){
					throw new Exception("err save png");
				}
				break;
			case IMAGETYPE_BMP:
				if(!imagebmp($canvas, dirname(__FILE__).self::IMG_DIR.$name)){
					throw new Exception("err save bmp");
				}
				break;
			default:
				throw new Exception("err save file:".$name);
		}

		imagedestroy($image);
		imagedestroy($canvas);
	}
	public function getAllPhoto(int $id, int $category){
		$path = ORM::for_table("travel_img")
		->select("file_name")
		->where(["pin_id" => $id, "file_delete" => 0, "img_category" => $category])->find_array();
		return $path;
	}
	private function checkValidFileSize(string $bin){
		$size_limit = 10 * 1024 * 1024;
		return strlen($bin) <= $size_limit;
	}
	private function checkValidFileName(string $name){
		$file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		$allowed_ext = ["jpg", "jpeg", "bmp", "png"];
		return in_array($file_ext, $allowed_ext);
	}
	private function checkValidMimeType(string $mime){
		$allowed_mime_types = ["image/jpeg", "image/png", "image/bmp"];
		return in_array($mime, $allowed_mime_types);
	}
}
