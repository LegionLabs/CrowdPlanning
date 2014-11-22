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

date_default_timezone_set('America/New_York');


$action = $_REQUEST['action'];

if( !empty($action) ){

    switch($action){
        case 'save_action':
            user_action_save();
            break;
        case 'update_action':
            user_action_save_step2();
            break;
        case 'load_simple':
            response_in_process();
            break;
        case 'load_proccessed':
            response_processed();
            break;
    }

}
else{
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
    $user = $_REQUEST['user'];

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
        $result = $sth->execute(array(':action_name' => $action, ':action_datetime' => $date, ':action_status' => 1, ':action_session'=>$session, ':action_userid' => $user));

        if($result){
            $data = get_session_actions($session);
            json_response(false,$data);
        }


    }else{
        json_response(true,'value missing please provide value!');
    }

}

/*
 * function to save value inserted by the user for the step 2
 */
function user_action_save_step2(){

    $length    = 50;
    $action    = $_REQUEST['value'];
    $session   = $_REQUEST['session'];
    $user      = $_REQUEST['user'];
    $date      = strtotime(date('H:i:s')) ;

    if(!empty($action)){

        $db = connection();

        $data = get_data_suggestions($action);

        $sth  = $db->prepare("INSERT INTO libraries (lib_name, lib_datetime, lib_status, lib_session, lib_userid) VALUES(:action_name,:action_datetime,:action_status,:action_session,:action_userid)");
        $result = $sth->execute(array(':action_name' => $data['action_name'], ':action_datetime' => $data['action_datetime'], ':action_status' => 1, ':action_session'=>$data['action_session'], ':action_userid' => $data['action_userid']));

        if($result){
            $data = get_session_libraries($session);
            remove_entery_actions($action);
            json_response(false,$data);
        }

    }
    else{
        json_response(true,'value missing please provide value!');
    }
}


/*
 * function to load db connection
 */

function connection(){

    return load_database('./');

}

// WSL: New
function response_in_process(){


    $session   = $_REQUEST['session'];
    $user = $_REQUEST['user'];

    $user_id = getUserid($user);

    //$data = get_user_actions($user_id,0);
    $data = get_session_actions($session,0);
    json_response(false,$data);

}



function response_processed(){

    $session   = $_REQUEST['session'];
    $user   = $_REQUEST['user'];

    $user_id = getUserid($user);


    //$data = get_user_actions($user_id,1);
    $data = get_session_libraries($session);
    json_response(false,$data);

}


function get_data_suggestions($id){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM actions WHERE action_id=:actionid LIMIT 1");
    $sth->execute(array(':actionid'=>$id));

    $data =  array();

    while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

        $cursor = PDO::FETCH_ORI_NEXT;
        $data[] = $row;

    }

    if(is_array($data)){
        return $data[0];
    }else{
        return false;
    }

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


// WSL: New
function get_user_actions($userid,$status){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM actions WHERE action_userid=:userName and action_status=:status LIMIT 30");
    $sth->execute(array(':userName'=>$userid,':status'=>$status));

    $data =  array();

    while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

        $cursor = PDO::FETCH_ORI_NEXT;
        $data[] = $row;

    }

    return $data;

}


// WSL-TODO: Figure out if we need to track based on status here!
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


function get_session_libraries($session){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM libraries WHERE lib_session=:session LIMIT 30");
    $sth->execute(array(':session'=>$session));

    $data =  array();

    while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

        $cursor = PDO::FETCH_ORI_NEXT;
        $data[] = $row;

    }

    return $data;

}

/*
 * Remove approved action from actions table
 */

function remove_entery_actions($id){

    $db = connection();

    $sth = $db->prepare("DELETE FROM actions where action_id =:id");
    $result = $sth->execute(array(':id' =>$id));

    return $result;

}
