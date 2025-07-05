<?php
namespace Src;
use GuzzleHttp\Client;
class Login{
  private $header = null;
	public function __construct(){
    $this->header = getallheaders();
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
	}

  public function validateCsrf(){
    $token = $this->header["X-Csrf-Token"] ?? "";
    return $_SERVER["REQUEST_METHOD"] !== "GET" &&
      isset($_SESSION["csrf_token"]) &&
      hash_equals($_SESSION["csrf_token"], $token);
  }

  public function login(string $pass){
    $host = $this->header["Host"] ?? "";
    $url = "https://".$host."/util_api/login.php";
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
