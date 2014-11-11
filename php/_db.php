<?php
  function load_database($pathStr) {
    //echo("Connecting to DB...");
    $dbh = new PDO("sqlite:".$pathStr."db/planner.db");
    //echo("Got DB handle.");
    return $dbh;
  }

?>
