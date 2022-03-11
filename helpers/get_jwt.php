<?php

function get_jwt($token){
  $check_token = preg_match('/Bearer\s(\S+)/',$token,$matches);
  return $check_token == 0? false : $matches[1];
}

?>
