<?php

ini_set("display_errors",1);

require './../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include_once("./../../helpers/status_codes.php");
include_once("./../../models/User.php");
include_once("./../../config/database.php");
include_once("./../../helpers/get_jwt.php");
include_once("./../../models/Note.php");
include_once("./../../helpers/note_exits.php");
include_once("./test.php");

class Serializer {

  private $needed_attributes;

  public function __construct($needed_attributes){
    $this->needed_attributes = $needed_attributes;
  }

  public function tuple($result){
    if($result->num_rows == 0){
      return [];
    }else {
      $data = [];
      while($row = $result->fetch_assoc()){
        $values = [];
        foreach($this->needed_attributes as $attr){
          $data[$attr] = $row[$attr];
        }
      }
      return $data;
    }
  }

  public function dump_all($result){
    if($result->num_rows == 0){
      return [];
    }else {
      $data = [];
      while($row = $result->fetch_assoc()){
        $values = [];
        foreach($this->needed_attributes as $attr){
          $values[$attr] = $row[$attr];
        }

        array_push($data,$values);
      }
      return $data;
    }
  }
}

$controller = new WonderController();


// $controller->protected_controller("POST",function($jwt,$body){
//   $user_id = null;
//
//   try{
//     $secret_key = "owt125";
//     $user_id = (JWT::decode($jwt, new Key($secret_key,"HS512")))->data->id;
//
//   }catch(\Firebase\JWT\ExpiredException $ex){
//     http_response_code(500);
//     echo json_encode(array(
//       'error' => true,
//       'message' => $ex->getMessage()
//     ));
//   }
//
//   if($user_id) {
//
//     if(empty($body->title) || empty($body->body)){
//       http_response_code(400);
//       echo json_encode(array(
//         'error' => true,
//         'message' => "All values needed",
//       ));
//     }else {
//       $connection = (new Database('localhost','root','','note_db'))->connect();
//       $note = new Note($connection);
//       if($note->create_note($body->title,$body->body,$user_id)){
//         http_response_code(201);
//         echo json_encode(array(
//           'error' => false,
//           'message' => "note created succefully"
//         ));
//       }else{
//           http_response_code(500);
//         echo json_encode(array(
//           'error' => true,
//           'message' => "failed to create note",
//         ));
//       }
//     }
//   }
// });

$controller->protected_controller("GET",function($jwt,$body) {
  $serializer = new Serializer(["id","title","body","user_id","created_at"]);
  $user_id = null;
  try{
    $secret_key = "owt125";
    $user_id = (JWT::decode($jwt, new Key($secret_key,"HS512")))->data->id;

  }catch(\Firebase\JWT\ExpiredException $ex){
    http_response_code(500);
    echo json_encode(array(
      'error' => true,
      'message' => $ex->getMessage()
    ));
  }

  if($user_id) {
    $note_id = isset($_GET['id'])? $_GET['id'] : false;
    if(!$note_id){
      http_response_code(400);
      echo json_encode(array(
        'error' => true,
        'message' => "id missing"
      ));
    }else if(!is_numeric($note_id)){
      http_response_code(400);
      echo json_encode(array(
        'error' => true,
        'message' => "note id should be numeric"
      ));
    }else if($note_id) {
      $connection = (new Database('localhost','root','','note_db'))->connect();
      $note = new Note($connection);
      $user_note = $note->get_note($user_id,$note_id);
      $user_notes = $note->get_all_notes($user_id);

      if(!$user_note){
        http_response_code(500);
        echo json_encode(array(
          'error' => true,
          'message' => "something went wrong working on it"
        ));
      }else if($user_note->num_rows == 0){
        http_response_code(200);
        echo json_encode(array(
          'error' => false,
          'data' => []
        ));
      }else {
        http_response_code(200);
        echo json_encode(array(
          'error' => false,
          'data' => $serializer->dump_all($user_notes)
        ));
      }
    }else {
        http_response_code(400);
        echo json_encode(array(
          'error' => true,
          'message' => "note id needed"
        ));
    }
  }
});


?>
