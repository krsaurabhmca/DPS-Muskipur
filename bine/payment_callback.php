<?php
require_once('required/function.php');

// PhonePe Configuration
$merchant_id = "SU2509191800322669506083";
$salt_key = "5c5a0b98-69ee-4edc-a248-cc23a74b6288";
$salt_index = 1;

// Get callback data
$callback_data = json_decode(file_get_contents('php://input'), true);

// Verify callback signature
if(isset($callback_data['response'])) {
    $response = base64_decode($callback_data['response']);
    $response_array = json_decode($response, true);
    
    // Verify X-VERIFY
    $received_signature = $callback_data['x-verify'] ?? '';
    $expected_signature_string = $response . $salt_key;
    $expected_signature = hash('sha256', $expected_signature_string) . '###' . $salt_index;
    
    if($received_signature === $expected_signature) {
        // Signature verified - process callback
        $merchant_transaction_id = $response_array['data']['merchantTransactionId'];
        
        // Update transaction status
        $update_data = array(
            'status' => $response_array['code'] == 'PAYMENT_SUCCESS' ? 'COMPLETED' : 'FAILED',
            'payment_response' => json_encode($response_array),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        update_data('phonepe_transactions', $update_data, $merchant_transaction_id, 'merchant_transaction_id');
        
        // Return success response to PhonePe
        http_response_code(200);
        echo json_encode(array('status' => 'OK'));
    } else {
        // Invalid signature
        http_response_code(400);
        echo json_encode(array('status' => 'INVALID_SIGNATURE'));
    }
} else {
    http_response_code(400);
    echo json_encode(array('status' => 'INVALID_REQUEST'));
}
?>