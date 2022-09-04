<?php
require(dirname(__FILE__)."/../vendor/autoload.php");
use Src\Photo;
if(array_key_exists("bin", $_POST)){
	$photo = new Photo;
	$photo->putFile($_POST["name"], $_POST["bin"]);
}

?>
