<?php
function getGlobalTime() {
  $microtime = microtime();
  $comps = explode(' ', $microtime);
  $millis = sprintf('%d%03d', $comps[1], $comps[0] * 1000);
  date_default_timezone_set("America/New_York");  // Recommended by PHP
  $server_time = $millis + (-1*(((int)date("P"))*60*60)*1000);
  return $server_time;
}
?>
