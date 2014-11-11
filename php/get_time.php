<?php
include('../php/_time.php');

$millis = getGlobalTime();

if(isset($_REQUEST['t'])) {
  $t = $_REQUEST['t'];
} else {
  $t = $millis;
}

$offset = $millis - $t;

echo $offset . ":" . $t . ":" . $millis;
?>