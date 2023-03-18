<?php
namespace Src;
use ORM;
use Dotenv;
class Airport{
  public function __construct(){
    $env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
    $env->load();
    ORM::configure("mysql:host=localhost;charset=utf8;dbname=".$_ENV["DB_DB"]);
    ORM::configure("username", $_ENV["DB_USER"]);
    ORM::configure("password", $_ENV["DB_PASS"]);
  }

  public function getAllDeparture(){
    return ORM::for_table("airport_point")
    ->select("*")
    ->find_array();
  }
}

?>
