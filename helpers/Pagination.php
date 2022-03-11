<?php

class Pagination {

  private $connection;
  private $page;
  private $results_per_page;
  private $tbl_name;
  private $needed_attributes;
  private $user_id;

  // ,$user_id,$needed_attributes,$page=1,$results_per_page=10

  public function __construct($connection,$tbl_name,$needed_attributes,$params){
    $this->connection = $connection;
    // $this->page = $page;
    // $this->results_per_page = $results_per_page;
    $this->tbl_name = $tbl_name;
    $this->needed_attributes = $needed_attributes;
    // $this->user_id = $user_id;
    $this->page = $params->page?$params->page : 1;
    $this->results_per_page = $params->results_per_page? $params->results_per_page:10;
    $this->user_id = $params->user_id ? $params->user_id : null;



  }

  private function get_data(){
    if($this->user_id){
      $query = "SELECT * FROM $this->tbl_name WHERE user_id = ?";
      $stmt = $this->connection->prepare($query);
      $stmt->bind_param("i",$this->user_id);
      $stmt->execute();
      return $stmt->get_result();
    }
    $query = "SELECT * FROM $this->tbl_name";
    $stmt = $this->connection->query($query);
    return $stmt;
  }

  private function tbl_row_length(){
    return $this->get_data()->num_rows;
  }

  private function get_page_results(){
    $number_of_results = $this->tbl_row_length();
    $page_results = ($this->page-1) * $this->results_per_page;
    return $page_results;
  }

  private function get_number_of_results(){
    return $this->tbl_row_length();
  }

  private function get_number_of_pages(){
    $number_of_results = $this->tbl_row_length();
    $number_of_pages = ceil($number_of_results/$this->results_per_page);
    return $number_of_pages;
  }

  private function get_page_data(){
    $page_results = $this->get_page_results();
    $stmt =null;
    $result = null;
    $data = array();
    if($this->user_id){
      $query = "SELECT * FROM $this->tbl_name WHERE user_id = ? LIMIT $page_results, $this->results_per_page";
      $stmt = $this->connection->prepare($query);
      $stmt->bind_param("i",$this->user_id);
      $stmt->execute();
      $result = $stmt->get_result();
    }else {
      $query = "SELECT * FROM $this->tbl_name LIMIT $page_results, $this->results_per_page";
      $result = $this->connection->query($query);
    }


    while($row = $result->fetch_assoc()){
      $entity = array();
      foreach ($this->needed_attributes as $item) {
        $entity[$item] = $row[$item];
      }
      array_push($data,$entity);
    }
    return $data;
  }

  public function meta_data(){
    return array(
      'data' => $this->get_page_data(),
      'current_page' => $this->page,
      'total_results' => $this->get_number_of_results(),
      'number_of_pages' => $this->get_number_of_pages()
    );
  }

}


 ?>
