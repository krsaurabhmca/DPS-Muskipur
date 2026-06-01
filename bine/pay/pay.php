<?php
require('../function.php');
require('config.php');
require('razorpay-php/Razorpay.php');
//session_start();

// Create the Razorpay Order
use Razorpay\Api\Api;
?>
<style>
body{
    background:azure;
    text-align:center;
    margin-top:20%;
    font-size:36px;
}
#rzp-button1{
    width:260px;
    line-height:40px;
    font-size:20px;
    background:#ffc107;
    color:#222;
    border-radius:6px;
    border:solid 1px #ffc107;
}
</style>

<?php


$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
extract($_POST);
$amount_in_rs = $amount;
$amount = $amount*100;

$orderData = [
    'receipt'         => $pay_req_no,
    'amount'          => $amount, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

update_data('online_payment', array('order_id'=>$razorpayOrderId,'payment_amount'=>$amount_in_rs),$pay_req_no, 'demand_id');

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$checkout = 'automatic';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => $inst_name,
    "description"       => $fee_details,
    "image"             => $logo_url,
    "prefill"           => [
    "name"              => $student_name,
    "email"             => $student_email,
    "contact"           => $student_mobile,
    ],
    "notes"             => [
    "address"           => $inst_address,
    "merchant_order_id" => $order_no,
    ],
    "theme"             => [
    "color"             => "#ffc107"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");
