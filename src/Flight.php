<?php
namespace Src;
use ORM;
use Dotenv;
class Flight{
  public function __construct(){
    $env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
    $env->load();
    ORM::configure("mysql:host=localhost;charset=utf8;dbname=".$_ENV["DB_DB"]);
    ORM::configure("username", $_ENV["DB_USER"]);
    ORM::configure("password", $_ENV["DB_PASS"]);
  }

  public function getAllRoute(int $id){
    return ORM::for_table("flight_route")
    ->select("*")
    ->where("station_name", $st_name)
    ->find_array();
  }

  public function addFlightRoute(int $departure_id, int $destination_id){
    $route = ORM::for_table("flight_route")->create();
    $route->base_airport_id = $departure_id;
    $route->destination_id = $destination_id;
    $route->save();
    return $route->id();
  }
}

?>
