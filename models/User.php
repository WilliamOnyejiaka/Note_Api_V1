<?php
ini_set("display_errors",1);

class User {

  private $name;
  private $email;
  private $password;
  private $connection;

  public function __construct($connection){
    $this->connection  = $connection;
  }

  public function create_user($name,$email,$password){
    $query = "INSERT INTO users SET name = ?, email = ?, password = ?";
    $obj = $this->connection->prepare($query);
    $password = password_hash($password,PASSWORD_DEFAULT);
    $name = htmlspecialchars(strip_tags($name));
    $password = htmlspecialchars(strip_tags($password));
    $email = htmlspecialchars(strip_tags($email));
    $obj->bind_param("sss",$name,$email,$password);
    return $obj->execute()? true : false;
  }

  public function get_user($email){
    $query = "SELECT * FROM users WHERE email = ?";
    $obj = $this->connection->prepare($query);
    $obj->bind_param("s",$email);
    if($obj->execute()){
      return $obj->get_result()->fetch_assoc();
    }
    return false;
  }

}
//
// $user = new User();
//
// $user->create_user("Micky","email","password");

?>
