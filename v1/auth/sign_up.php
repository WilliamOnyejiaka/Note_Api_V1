<?php

ini_set("display_errors",1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
  header("HTTP/1.1 200 OK");
  die();
}

include_once("./../../config/config.php");

include_once("./../../helpers/status_codes.php");
include_once("./../../models/User.php");
include_once("./../../config/database.php");
include_once("./../../helpers/email_is_valid.php");


if($_SERVER['REQUEST_METHOD'] == "POST"){
  $user = new User((new Database($host,$username,$password,$database_name))->connect());
  $body = json_decode(file_get_contents("php://input"));

  if(empty($body->name) || empty($body->email) || empty($body->password)){
    http_response_code($HTTP_401_UNAUTHORIZED);
    echo json_encode(array(
      'error' => true,
      'message' => "All values needed"
    ));
  }else {
    if($user->get_user($body->email)){
      http_response_code($HTTP_400_BAD_REQUEST);//$HTTP_409_CONFLICT
      echo json_encode(array(
        'error' => true,
        'message' => "email exits try another one"
      ));

    }else if($email_is_valid($body->email)){
      http_response_code($HTTP_400_BAD_REQUEST);
      echo json_encode(array(
        'error' => true,
        'message' => "email is not valid"
      ));
    }else if(strlen($body->email) > 80){
      echo json_encode(array(
        'error' => true,
        'message' => "email is too long"
      ));
    }else if(strlen($body->name) > 50){
      echo json_encode(array(
        'error' => true,
        'message' => "name is too long"
      ));
    }else {

      if($user->create_user($body->name,$body->email,$body->password)){
        http_response_code($HTTP_201_CREATED);
        echo json_encode(array(
          'error' => false,
          'message' => "user created successfully"
        ));
      }else {
        http_response_code($HTTP_500_INTERNAL_SERVER_ERROR);
        echo json_encode(array(
          'error' => true,
          'message' => "failed to create user"
        ));
      }
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
