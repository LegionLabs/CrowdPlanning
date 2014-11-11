<?php
error_reporting(E_ALL);
session_start();

//$config = parse_ini_file("../scribe.ini", true);
$config = parse_ini_file("../stitchr.ini", true);

//$in_period = intval($config['worker']['in_period']);
//$out_period = intval($config['worker']['out_period']);
$period = intval($config['worker']['period']);

$id = session_id();

include("_db.php");
include("_session.php");
include('_time.php');
//include_once('spelling.php');

//$session = $id;
if( isset($_REQUEST['session']) ) {
  $session = $_REQUEST['session'];
  $numWorkers = getNumWorkers($session);

  $in_period = $period / $numWorkers;
  $out_period = $period - $in_period;
}
else {
  echo("No session specified!");
  return 1;
}


// Now, proceed to record the information
$line_cutoff = 20*1000; // server-side cut-off for lines to be considered adjacent



if( isset($_REQUEST['word']) && isset($_REQUEST['time']) && isset($_REQUEST['key']) && isset($_REQUEST['offset'])) {

  $word = preg_replace('/^[^\w\[\]]*|[^\w\[\]]*$/', '', $_REQUEST['word']);

  $local_time = $_REQUEST['time'];
  $line = $_REQUEST['line'];
  $baseline = $_REQUEST['baseline'];

  if(isset($_REQUEST['worker']) && $_REQUEST['worker'] != "") {
    $worker = $_REQUEST['worker'];
  } else {
    $worker = $id;
  }

  if(isset($_REQUEST['key'])) {
    $key = $_REQUEST['key'];
  } else {
    $key = $id . "_key";
  }

  $offset = $_REQUEST['offset'];

  if(isset($_REQUEST['hit'])) {
    $hit = $_REQUEST['hit'];
  } else {
    $hit = $key . "_hit";
  }

  if(isset($_REQUEST['page_id'])) {
    $page_id = $_REQUEST['page_id'];
  } else {
    $page_id = $key . "_" . $id . "_page";
  }

  $server_time = getGlobalTime();


  /*
  //send to the combiner
  $socket = socket_create(AF_INET, SOCK_STREAM, 0);
  socket_bind($socket, "127.0.0.1");
  */


  $newCombiner = true;
  //if it's a new combiner run then enters are allowed, otherwise it needs to be a non-enter

  $enter = ($newCombiner || (!$newCombiner && !strstr($word, "EENNTTEERR")));

  // TODO: ADD SESSION PORT READ HERE!!
  if( $enter && !strstr($word, "INIT") && !strstr($word, "HHEERREE") ) { //&& socket_connect( $socket , "127.0.0.1" , 5555 )){
	$roundedTime = round(floatval($local_time));

	//if we want to send the global time stamps to Scribe
	if(isset($_REQUEST['gTime'])){
		if($_REQUEST['gTime'] == "true"){
			$roundedTime = round(floatval($server_time));
		}
	}

    //$period = $in_period + $out_period;
    //$segmentNumber = floor((($local_time - ($offset * $in_period)) / $period) * ($period / $in_period)) + $offset;
    $segmentNumber = (floor(($local_time - ($offset * $in_period)) / $period) * floor($period / $in_period)) + $offset;
    $combinerInput = preg_replace("/:+/", ":", $worker) . "_" . preg_replace("/:+/", ":", $offset) . ":" . preg_replace("/:+/", ":", $word) . ":" . preg_replace("/:+/", ":", $roundedTime) . ":" . preg_replace("/:+/", ":", $segmentNumber);

    print $combinerInput . "\n";

    //$logname = "combiner_input_log_" . $session . ".txt";
    //file_put_contents($logname, $combinerInput . PHP_EOL, FILE_APPEND);

    //socket_write($socket, $combinerInput);
  }
  //socket_close($socket);

  try {
    $dbh = getDatabaseHandle("../");
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

  if($dbh) {

    //$period = $in_period + $out_period;

    //$segnum = floor((($local_time - $offset * $in_period) / $period) * ($period / $in_period) + $offset);
    $segnum = (floor(($local_time - ($offset * $in_period)) / $period) * floor($period / $in_period)) + $offset;
    //print $local_time . "|" . $in_period . "|" . $period . "|" . "SEG: ".$segnum."|\n";

    //
    // Write the new word to the raw 'words' table.
    echo("\nWriting :: " . $worker . ", " . $word . ", " . $server_time . ", " . $offset . ", " . $segnum . ". to WORDS table\n");
    $sth = $dbh->prepare("INSERT INTO words (worker, session, word, time, offset, segnum) VALUES(:worker, :session, :word, :server_time, :offset, :segnum)");
    $sth->execute(array(':worker'=>$worker, ':session'=>$session, ':word'=>$word, ':server_time'=>$server_time, ':offset'=>$offset, ':segnum'=> $segnum));
    //
    // Stub the call to the combiner function/object method/service by copying
    // the appropriate fields to the 'final' table
    echo("\nWriting :: " . $worker . ", " . $word . ", " . $server_time . ", " . $offset . ", " . $segnum . ". to FINAL table\n");
    $sth = $dbh->prepare("INSERT INTO final (word, time, segnum, session) VALUES(:word, :server_time, :segnum, :session)");
    $sth->execute(array(':session'=>$session, ':word'=>$word, ':server_time'=>$server_time, ':segnum'=> $segnum));
    //

    echo("Error code(s): ");
    print $dbh->errorCode();

    echo("Added!\n");
    $word_id = $dbh->lastInsertId();

    echo '{"word_id": ' . $word_id . '}';
  }
}
?>
