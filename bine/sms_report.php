<?php
include_once('required/function.php');
  //  $data = json_decode(file_get_contents('php://input'), true);
    $_POST = json_decode(file_get_contents('php://input'), true);
    //echo "<pre>";
    //print_r($_POST);
    mail('myofferplant@gmail.com','SMS Delivery Report', json_encode($_POST));
    //MSG.MORG.IN SMS REPORT
    foreach($_POST as $data)
    {
         $res = update_data('op_sms', array('status'=>$data['status'],'delivery_time' =>$data['deliveredDateTime']), $data['requestId'], 'request_id');
         echo json_encode($res);
    }   
    
?>

