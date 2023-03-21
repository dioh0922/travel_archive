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
    ->select("travel_point.lat", "lat")
    ->select("travel_point.lng", "lng")
    ->select("travel_point.station_name", "name")
    ->join("travel_point", ["travel_point.pin_id", "=", "flight_route.destination_id"])
    ->where("base_airport_id", $id) 
    ->group_by("travel_point.pin_id")
    ->find_array();
  }

  public function addFlightRoute(int $departure_id, int $destination_id){
    $route = ORM::for_table("flight_route")->create();
    $route->base_airport_id = $departure_id;
    $route->destination_id = $destination_id;
    $route->save();
    return $route->id();
  }
  
  public function chkExistRoute(int $departure, int $destination){
    return ORM::for_table("flight_route")
    ->where(["base_airport_id" => $departure, "destination_id" => $destination])
    ->find_one();
  }
}

?>
