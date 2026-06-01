<?php
require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

 $paymentId = 'pay_IV12Kq7MYyArta';
 $tamt =100;
 $accountId ='acc_IT54MPIjnnhmb7';

//  $res = $api->payment->fetch($paymentId)->transfer(array('transfers' => array('account'=> $accountId, 'amount'=> $tamt, 'currency'=>'INR', 'notes'=> array('name'=>'Gaurav Kumar', 'roll_no'=>'IEC2011025'), 'linked_account_notes'=>array('branch'), 'on_hold'=>'1', 'on_hold_until'=>'1671222870')));

// echo "<pre>";

// //$res= $api->transfer->create(array('account' => $accountId, 'amount' => $tamt, 'currency' => 'INR'));


// $api = new Api($key_id, $secret);

$api->order->create(array('amount' => 2000,'currency' => 'INR','transfers' => array(array('account' => $accountId,'amount' => 1000,'currency' => 'INR','notes' => array('branch' => 'Acme Corp Bangalore North','name' => 'Gaurav Kumar'),'linked_account_notes' => array('branch'),'on_hold' => 1,'on_hold_until' => 1671222870),array('account' => $accountId,'amount' => 1000,'currency' => 'INR','notes' => array('branch' => 'Acme Corp Bangalore South','name' => 'Saurav Kumar'),'linked_account_notes' => array('branch'),'on_hold' => 0))));


//print_r($res);
