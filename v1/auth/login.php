<?php
ini_set("display_errors",1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// header('WWW-Authenticate: Basic realm="Note Api"');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
  header("HTTP/1.1 200 OK");
  die();
}

require './../../vendor/autoload.php';
use \Firebase\JWT\JWT;

include_once("./../../helpers/status_codes.php");
include_once("./../../models/User.php");
include_once("./../../config/database.php");

if($_SERVER['REQUEST_METHOD'] == "GET") {
  $user = new User((new Database("localhost","root","","note_db"))->connect());
  $email = $_SERVER['PHP_AUTH_USER'];
  $password = $_SERVER['PHP_AUTH_PW'];

  if(!$email || !$password) {
    http_response_code($HTTP_401_UNAUTHORIZED);
    echo json_encode(array(
      'error' => true,
      'message' => "All values needed"
    ));
  }else {
    $user_data = $user->get_user($email);
    if($user_data){

      if(password_verify('password', $user_data['password'])){
        $iat = time();
        $nbf = $iat;
        $exp = $iat + 3600;
        $aud = "myusers";
        $user_data_arr = array(
          'id' => $user_data['id'],
          'name' => $user_data['name'],
          'email' =>$user_data['email']
        );
        $secret_key = "owt125";

        $payload = array(
          'iat' => $iat,
          'nbf' => $nbf,
          'exp' => $exp,
          'aud' => $aud,
          'data' => $user_data_arr
        );

        $jwt = JWT::encode($payload,$secret_key,'HS512');

        http_response_code($HTTP_202_ACCEPTED);
        echo json_encode(array(
          'error' => false,
          'jwt' => $jwt
        ));
      }
    }else {
      http_response_code($HTTP_401_UNAUTHORIZED);
      echo json_encode(array(
        'error' => true,
        'message' => "user does not exist"
      ));
    }
  }
}else {
  http_response_code($HTTP_405_METHOD_NOT_ALLOWED);
  echo json_encode(array(
    'error' => true,
    'message' => "Access Denied"
  ));
}
?>
