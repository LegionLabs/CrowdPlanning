<?php

function getNumWorkers($sName) {
  try {
    $dbh = getSessionDatabaseHandle("../");
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

  if($dbh) {
    // Find the period sizes
    $sth = $dbh->prepare("SELECT numworkers FROM sessions WHERE session=:sessionName OR tempmask=:sessionName ORDER BY lastupdated DESC LIMIT 1");
    $sth->execute(array(':sessionName'=>$sName));

    $row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
//echo("HERE: ".$row['numworkers']);
    $numWorkers = intval($row['numworkers']);
//echo("THERE::" . $numWorkers);
    return $numWorkers;
  }
  else {
    echo("Failed to acquire session handle!");
    return null;
  }
}

?>
