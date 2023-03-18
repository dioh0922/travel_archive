<?php
namespace Src;
use ORM;
use Dotenv;
class Point{
	public function __construct(){
		$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
		$env->load();
		ORM::configure("mysql:host=localhost;charset=utf8;dbname=".$_ENV["DB_DB"]);
		ORM::configure("username", $_ENV["DB_USER"]);
		ORM::configure("password", $_ENV["DB_PASS"]);
	}
	public function savePoint(string $st_name, string $lat, string $lng){
		$point = ORM::for_table("travel_point")->create();
		$point->station_name = $st_name;
		$point->lat = (double)$lat;
		$point->lng = (double)$lng;
		$point->save();
		return $point->id();
	}
	public function saveImgPath(int $id, string $img_path, int $category){
		$path = ORM::for_table("travel_img")->create();
		$path->pin_id = $id;
		$path->file_name = $img_path;
		$path->img_category = $category;
		$path->save();
		return $path->id();
	}
	public function checkExist(string $st_name){
		$exist = ORM::for_table("travel_point")->where("station_name", $st_name)->find_one();
		return $exist;
	}
	public function getAllPin(int $category){
		//カテゴリの写真が1以上あるならピンうつ
		$list = ORM::for_table("travel_point")
		->select("*")
		->join("travel_img", ["travel_point.pin_id", "=", "travel_img.pin_id"])
		->where(["is_deleted" => 0, "travel_img.img_category" => $category])
		->group_by("travel_point.pin_id")
		->find_array();
		return $list;
	}
	public function getAllDeparture(){
		return ORM::for_table("travel_point")
		->select("*")
		->where("is_departure", 1)
		->find_array();
	}
}
