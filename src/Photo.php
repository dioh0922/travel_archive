<?php
namespace Src;
use ORM;
use Dotenv;

class Photo{
	private const IMG_DIR = "/../img/";
	public function __construct(){
		$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
		$env->load();
		ORM::configure("mysql:host=localhost;charset=utf8;dbname=".$_ENV["DB_DB"]);
		ORM::configure("username", $_ENV["DB_USER"]);
		ORM::configure("password", $_ENV["DB_PASS"]);
	}
	public function putFile(string $name, string $bin){
		$data = base64_decode($bin);
		file_put_contents(dirname(__FILE__).self::IMG_DIR.$name, $data);
	}
	public function getAllPhoto(int $id, int $category){
		$path = ORM::for_table("travel_img")
		->select("file_name")
		->where(["pin_id" => $id, "file_delete" => 0, "img_category" => $category])->find_array();
		return $path;
	}
}
