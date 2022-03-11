<?php

class Note {

  private $connection;

  public function __construct($connection){
    $this->connection = $connection;
  }

  public function create_note($title,$body,$user_id){
    $query = "INSERT INTO notes SET title = ? ,body = ? , user_id = ?";
    $obj = $this->connection->prepare($query);
    $title = htmlspecialchars(strip_tags($title));
    $body = htmlspecialchars(strip_tags($body));
    $user_id = htmlspecialchars(strip_tags($user_id));
    $obj->bind_param("ssi",$title,$body,$user_id);
    return $obj->execute()?true:false;
  }

  public function get_note($user_id,$id){
    $query = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
    $obj = $this->connection->prepare($query);
    $user_id = htmlspecialchars(strip_tags($user_id));
    $id = htmlspecialchars(strip_tags($id));
    $obj->bind_param("ii",$id,$user_id);
    return $obj->execute()? $obj->get_result():false;
  }

  public function get_all_notes($user_id){
    $query = "SELECT * FROM notes WHERE user_id = ?";
    $obj = $this->connection->prepare($query);
    $user_id = htmlspecialchars(strip_tags($user_id));
    $obj->bind_param("i",$user_id);
    return $obj->execute()? $obj->get_result():false;
  }

  public function update_title($user_id,$id,$title){
    $query = "UPDATE notes SET title = ? WHERE id = ? AND user_id = ?";
    $obj = $this->connection->prepare($query);
    $user_id = htmlspecialchars(strip_tags($user_id));
    $id = htmlspecialchars(strip_tags($id));
    $title = htmlspecialchars(strip_tags($title));
    $obj->bind_param("sii",$title,$id,$user_id);
    return $obj->execute()? true:false;
  }

  public function update_body($user_id,$id,$body){
    $query = "UPDATE notes SET body = ? WHERE id = ? AND user_id = ?";
    $obj = $this->connection->prepare($query);
    $user_id = htmlspecialchars(strip_tags($user_id));
    $id = htmlspecialchars(strip_tags($id));
    $body = htmlspecialchars(strip_tags($body));
    $obj->bind_param("sii",$body,$id,$user_id);
    return $obj->execute()? true:false;
  }

  public function update_note($user_id,$id,$title,$body){
    $query = "UPDATE notes SET title = ?,body = ? WHERE id = ? AND user_id = ?";
    $obj = $this->connection->prepare($query);
    $user_id = htmlspecialchars(strip_tags($user_id));
    $id = htmlspecialchars(strip_tags($id));
    $title = htmlspecialchars(strip_tags($title));
    $body = htmlspecialchars(strip_tags($body));
    $obj->bind_param("ssii",$title,$body,$id,$user_id);
    return $obj->execute()? true:false;
  }

  public function delete_note($user_id,$id){
    $query = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    $obj = $this->connection->prepare($query);
    $id = htmlspecialchars(strip_tags($id));
    $user_id = htmlspecialchars(strip_tags($user_id));
    $obj->bind_param("ii",$id,$user_id);
    return $obj->execute()?true:false;
  }

  // public function note_exists($user_id,$id){
  //   $query = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
  //   $stmt = $this->connection->prepare($query);
  //   $user_id = htmlspecialchars(strip_tags($user_id));
  //   $id = htmlspecialchars(strip_tags($id));
  //   $stmt->bind_param("ii",$id,$user_id);
  //   return $stmt->get_result();
  // }
}

?>
