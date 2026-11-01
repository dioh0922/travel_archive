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
    $env = Dotenv\Dotenv::createImmutable(dirname(__FILE__)."/../../env");
    $env->load();

    $url = $_ENV["INNER_UTIL_URL"] . "/api/util/login";
    $post_data = ["password" => $pass, "inner" => true];
    $client = new Client();
    $response = $client->post($url, [
      "json" => $post_data,
      "headers" => [
        "Content-Type" => "application/json"
      ]
    ]);
    $body = $response->getBody();
    $json = json_decode($body, true);

    $success = $json["success"] ?? false;
    /* ext_apiはsuccessで直接返る */
    // $success = isset($json["result"]) && $json["result"] === 1;
    if($success){
      $_SESSION["login"] = "on";
    }
    return $success;
  }

}

?>
