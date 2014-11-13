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


if(!empty($action)){

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
    $user_hash = md5('somehash');
    $date      = strtotime(date('H:i:s')) ;

    if(!empty($action)){

        $db = connection();

        if( strlen($action) > $length ){

            json_response(true,'Max length allowed :'.$length);

        }

        $user_id = getUserid($session);

        if($user_id != false){

            $sth = $db->prepare("INSERT INTO actions (action_name, action_datetime, action_status, action_userid) VALUES(:action_name,:action_datetime,:action_status,:action_userid)");
            $result = $sth->execute(array(':action_name' => $action, ':action_datetime' => $date, ':action_status' => 0, ':action_userid' => $user_id));

            if($result){

                $data = get_user_actions($user_id,0);
                json_response(false,$data);

            }

        }else{
            json_response(true,'user sessions doesnt exists!');
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
    $date      = strtotime(date('H:i:s')) ;

    if(!empty($action)){

        $db = connection();

        $user_id = getUserid($session);

        if($user_id != false){

            $sth = $db->prepare("UPDATE actions set action_status = :status where action_id = :action_id_passed");
            $result = $sth->execute(array(':status' => 1, ':action_id_passed' => $action));

            if($result){

                $data = get_user_actions($user_id,1);
                json_response(false,$data);

            }

        }else{
            json_response(true,'user sessions doesnt exists!');
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

function response_in_process(){


    $session   = $_REQUEST['session'];

    $user_id = getUserid($session);

    $data = get_user_actions($user_id,0);
    json_response(false,$data);

}

function response_processed(){

    $session   = $_REQUEST['session'];

    $user_id = getUserid($session);

    $data = get_user_actions($user_id,1);
    json_response(false,$data);

}

function get_user_actions($userid,$status){

    $db = connection();

    $sth = $db->prepare("SELECT * FROM actions WHERE action_userid=:sessionName and action_status=:status LIMIT 30");
    $sth->execute(array(':sessionName'=>$userid,':status'=>$status));

    $data =  array();

        while (false !== ($row = $sth->fetch(PDO::FETCH_ASSOC, $cursor))) {

            $cursor = PDO::FETCH_ORI_NEXT;
            $data[] = $row;

        }

    return $data;

}