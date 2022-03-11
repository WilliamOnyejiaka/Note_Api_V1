<?php

function note_exits($result){
  return $result->num_rows > 0? true : false;
}
?>
