<?php
namespace Src;
use GuzzleHttp\Client;
class Login{
	public function __construct(){
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
	}

  public function validateCsrf(){
    $header = getallheaders();
    $token = $header["X-Csrf-Token"] ?? "";
    return $_SERVER["REQUEST_METHOD"] !== "GET" &&
      isset($_SESSION["csrf_token"]) &&
      hash_equals($_SESSION["csrf_token"], $token);
  }

  public function login(string $pass){
    $url = "https://".gethostbyname()."/util_api/login.php";
    $post_data = ["pass" => $pass];
    $client = new Client();
    $response = $client->post($url, [
      "json" => $post_data,
    ]);
    $body = $response->getBody();
    $json = json_decode($body, true);
    return isset($json["result"]) && $json["result"] === "1";
  }

}

?>
