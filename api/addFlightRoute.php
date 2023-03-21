<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
session_start();
use Src\Flight;
use Src\Point;
$result = ["result" => 0];

try{
  if(!array_key_exists("login", $_SESSION) || $_SESSION["login"] == null){
    throw new Exception("session expire");
  }
  $point = new Point();
  $destination_name = $_POST["name"];
  if(!$point->checkExist($destination_name)){
    $destination_id = $point->savePoint(
      $destination_name, 
      (double)$_POST["lat"], 
      (double)$_POST["lng"]
    );

    $departure_id = (int)$_POST["departure"];
    $flight = new Flight();
    $result["route_id"] = $flight->addFlightRoute($departure_id, $destination_id);
    $result["result"] = 1;

  }
}catch(Exception $e){
  $result["result"] = -1;
  $result["message"] = $e->getMessage();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>
