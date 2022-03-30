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

require './../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
include_once("./../../config/config.php");
include_once("./../../helpers/status_codes.php");
include_once("./../../models/User.php");
include_once("./../../config/database.php");
include_once("./../../helpers/get_jwt.php");
include_once("./../../models/Note.php");

if($_SERVER['REQUEST_METHOD'] == "GET"){
  $token = isset((getallheaders())['Authorization'])?(getallheaders())['Authorization']:false;
  $jwt = get_jwt($token);

  if($token){
    if($jwt) {
      $user_id = null;
      try{
        $secret_key = "owt125";
        $user_id = (JWT::decode($jwt, new Key($secret_key,"HS512")))->data->id;

      }catch(\Firebase\JWT\ExpiredException $ex){
        http_response_code($HTTP_500_INTERNAL_SERVER_ERROR);
        echo json_encode(array(
          'error' => true,
          'message' => $ex->getMessage()
        ));
      }
      if($user_id) {
        $note_id = isset($_GET['id'])? $_GET['id'] : false;
        if(!is_numeric($note_id)){
          http_response_code($HTTP_400_BAD_REQUEST);
          echo json_encode(array(
            'error' => true,
            'message' => "note id should be numeric"
          ));
        }else if($note_id) {
          // $connection = (new Database('localhost','root','','note_db'))->connect();
          $connection = (new Database($host,$username,$password,$database_name))->connect();
          $note = new Note($connection);
          $user_note = $note->get_note($user_id,$note_id);

          if(!$user_note){
            http_response_code($HTTP_500_INTERNAL_SERVER_ERROR);
            echo json_encode(array(
              'error' => true,
              'message' => "something went wrong working on it"
            ));
          }else if($user_note->num_rows == 0){
            http_response_code($HTTP_200_OK);
            echo json_encode(array(
              'error' => false,
              'data' => []
            ));
          }else {
            http_response_code($HTTP_200_OK);
            echo json_encode(array(
              'error' => false,
              'data' => $user_note->fetch_assoc()
            ));
          }
        }else {
            http_response_code($HTTP_400_BAD_REQUEST);
            echo json_encode(array(
              'error' => true,
              'message' => "note id needed"
            ));
        }
      }
    }else {
      http_response_code($HTTP_400_BAD_REQUEST);
      echo json_encode(array(
        'error' => true,
        'message' => "invalid jwt"
      ));
    }
  }else {
    http_response_code($HTTP_401_UNAUTHORIZED);
    echo json_encode(array(
      'error' => true,
      'message' => "Authorization header missing"
    ));
  }

}else {
  http_response_code($HTTP_500_INTERNAL_SERVER_ERROR);
  echo json_encode(array(
    'error' => true,
    'message' => "Access Denied"
  ));
}

?>
