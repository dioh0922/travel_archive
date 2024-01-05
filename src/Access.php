<?php
namespace Src;
use ORM;
use Dotenv;
class Access{
	public function __construct(){
		$env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
		$env->load();
		ORM::configure("mysql:host=".$_ENV["DB_HOST"].";port=".$_ENV["DB_PORT"]."charset=utf8;dbname=".$_ENV["DB_DB"]);
		ORM::configure("username", $_ENV["DB_USER"]);
		ORM::configure("password", $_ENV["DB_PASS"]);
	}

	public function getCurrentCount(string $today){
		return ORM::for_table("g_access_count")->select("access_count")->where("access_day", $today)->find_one();
	}
	public function saveAccess(string $today){
		$access = ORM::for_table("g_access_count")->create();
		$access->access_day = $today;
		$access->save();
		return $access->id();
	}
	public function addAccess(string $today){
		$access = ORM::for_table("g_access_count")->select("*")->where("access_day", $today)->find_one();
		$access->access_count++;
		$access->save();
	}
}

?>
