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
  $flight = new Flight(); 
  $destination_name = $_POST["name"];
  $exist_destination = $point->checkExist($destination_name);
  $destination_id = 0;
  $departure_id = (int)$_POST["departure"]; 
  if(!$exist_destination){
    $destination_id = $point->savePoint(
      $destination_name, 
      (double)$_POST["lat"], 
      (double)$_POST["lng"]
    );

    $departure_id = (int)$_POST["departure"];

  }else{
    $destination_id = $exist_destination->pin_id;
  }
  if(!$flight->chkExistRoute($departure_id, $destination_id)){
    $result["route_id"] = $flight->addFlightRoute($departure_id, $destination_id);
  }else{
    throw new Exception("すでに記録されている路線です");
  }
  $result["result"] = 1;
}catch(Exception $e){
  $result["result"] = -1;
  $result["message"] = $e->getMessage();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>
