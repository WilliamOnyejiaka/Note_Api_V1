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

include_once("./../../config/database.php");
include_once("./../../helpers/Pagination.php");


$page = isset($_GET['page'])? $_GET['page']: 1;
$results_per_page = isset($_GET['results_per_page'])? $_GET['results_per_page']: 10;


$connection = (new Database("localhost","root","","note_db"))->connect();
$needed_attributes = ["id","title","body"];
$data = (new Pagination($connection,"notes",31,$needed_attributes,$page,$results_per_page))->meta_data();

echo json_encode($data);


?>
