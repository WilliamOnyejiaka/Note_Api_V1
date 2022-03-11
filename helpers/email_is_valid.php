<?php

$email_is_valid = fn($email) => preg_match("/^([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/", $email) == 0? true:false;


?>
