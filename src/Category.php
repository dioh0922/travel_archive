<?php
namespace Src;
use ORM;
use Dotenv;
class Category{
  public function __construct(){
    $env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
    $env->load();
    ORM::configure("mysql:host=".$_ENV["DB_HOST"].";port=".$_ENV["DB_PORT"]."charset=utf8;dbname=".$_ENV["DB_DB"]);
    ORM::configure("username", $_ENV["DB_USER"]);
    ORM::configure("password", $_ENV["DB_PASS"]);
  }

  public function getCategory(){
    return ORM::for_table("travel_pin_category")->select("*")->find_array();
  }
}

?>
