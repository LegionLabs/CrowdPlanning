<?php

/**
 * Created by PhpStorm.
 * Ajax based requests handled thorugh this file
 * Based on methods
 * User: apple
 * Date: 11/11/14
 * Time: 12:04 AM
 */

include_once('./php/_db.php');

$action = $_REQUEST['action'];
$action = 'save_action';

if(!empty($action)){

    switch($action){

        case 'save_action':
            user_action_save();
            break;

    }

}else{

    json_response(true,'Action missing please provide action');

}

/*
 * global json response for every function
 */

function json_response($error,$msg){

    $response =  array(
        'error' =>  $error,
        'msg'   =>  $msg
    );

    echo json_encode($response);

    die;
}


/*
 * function to save value inserted by the user for action
 */
function user_action_save(){

    $length    = 50;
    $action    = $_REQUEST['value'];
    $session   = $_REQUEST['session'];
    $date      = strtotime(date('H:i:s')) ;
    if( isset($_REQUEST['user']) ) {
        $user_name = $_REQUEST['user'];
    }
    else {
        $user_name = md5('somehash');
    }

    if(!empty($action)){

        $db = connection();

        if( strlen($action) > $length ){

            json_response(true,'Max length allowed :'.$length);

        }

/*
        $user_id = getUserid($session);

        if($user_id != false){

        }else{
            json_response(true,'user sessions doesnt exists!');
        }
*/

        // WSL: For now, don't worry about the prior user sessions.

        $sth = $db->prepare("INSERT INTO actions (action_name, action_datetime, action_status, action_session, action_userid) VALUES(:action_name,:action_datetime,:action_status,:action_session,:action_userid)");
        $result = $sth->execute(array(':action_name' => $action, ':action_datetime' => $date, ':action_status' => 1, ':action_session'=>$session, ':action_userid' => $user_name));

        if($result){
            $data = get_session_actions($session);
            json_response(false,$data);
        }


    }else{
        json_response(true,'value missing please provide value!');
    }

}

/*
 * function to load db connection
 */

function connection(){

    return load_database('./');

}


// WSL-TODO: Instead of getting the userID based on prior sessions (so that we can recover previously-added actions), let's try to get all prior sessions that were created for/by that user. Note that this might require us to use BOTH userID and sessionID as keys in the session table so that a user named "Tim" creating the session "demo" does not overlap with someone named "Raja" creating a different session, also called demo. THIS IS A FUTURE TASK, not something we need to deal with right now.
/*
 * function to get id of the user
 */
function getUserid($session){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM users WHERE user_hash=:hash_code LIMIT 1");
    $sth->execute(array(':hash_code'=>$session));

    $data =  array();

    while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

        $cursor = PDO::FETCH_ORI_NEXT;
        $data[] = $row;

    }

    if(is_array($data) && isset($data[0]['user_id'])){
        return $data[0]['user_id'];
    }else{
        return false;
    }


}


/*
 * Get user actions based on provided session hash
 */
function get_session_actions($session){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM actions WHERE action_session=:session LIMIT 30");
    $sth->execute(array(':session'=>$session));

    $data =  array();

        while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

            $cursor = PDO::FETCH_ORI_NEXT;
            $data[] = $row;

        }

    return $data;

}
