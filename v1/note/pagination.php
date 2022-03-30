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
include_once("./../../config/database.php");
include_once("./../../helpers/Pagination.php");
include_once("./../../helpers/SearchPagination.php");

// require './../../vendor/autoload.php';
// $dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(__DIR__)));
// $dotenv->load();

$page = isset($_GET['page'])? $_GET['page']: 1;
$results_per_page = isset($_GET['results_per_page'])? $_GET['results_per_page']: 10;

// $host = $_ENV['DB_HOST'];
// $username = $_ENV['DB_USERNAME'];
// $password = $_ENV['DB_PASSWORD'];
// $database_name = $_ENV['DB_DATABASE'];
$connection = (new Database($host,$username,$password,$database_name))->connect();
$needed_attributes = ["id","title","body"];
$params = array(
  'page'=> $page ,
  'results_per_page' => $results_per_page ,
  'user_id' => 32
);
// $data = (new Pagination($connection,"notes",$needed_attributes,$params))->meta_data();
$body = "body";
$title = "title";
$search_params = [$body,$title];
$data = (new SearchPagination($connection,"notes",$needed_attributes,'get',$search_params,$params))->meta_data();
// $query = "SELECT * FROM notes WHERE user_id = 32 AND body LIKE '%JWT%' OR title LIKE '%JWT%' ";
// $stmt = $connection->query($query);
// // var_dump($stmt->fetch_assoc());
// $data = [];
// while ($row = $stmt->fetch_assoc()) {
//   $data[] = array(
//     'id' => $row['id'],
//     'title' => $row['title'],
//     'body' => $row['body'],
//     'user_id' => $row['user_id']
//   );
//
// }


echo json_encode($data);


?>
