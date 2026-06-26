<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include DPS-Muskipur DB library
require_once __DIR__ . '/../required/op_lib.php';
require_once 'db_connect.php';

// =========================
//  PhonePe Credentials
// =========================
$clientId      = "SU2509191800322669506083";
$clientSecret  = "5c5a0b98-69ee-4edc-a248-cc23a74b6288";
$clientVersion = 1;
// Production base URLs (from PhonePe SDK Constants.php)
$PHONEPE_BASE_OAUTH = "https://api.phonepe.com/apis";       // for OAuth token
$PHONEPE_BASE_PG    = "https://api.phonepe.com/apis/pg";    // for pay & status

// =========================
//  Get merchantOrderId
// =========================
$merchantOrderId = $_GET['merchantOrderId'] ?? $_POST['merchantOrderId'] ?? '';

if (!$merchantOrderId) {
    die("<h2 style='color:red;font-family:sans-serif;padding:20px;'>Invalid Request: merchantOrderId missing</h2>");
}

// Retrieve session data set during initiation
$student_id         = $_SESSION['student_id']      ?? '';
$selectedMonthsJson = $_SESSION['selected_months'] ?? '[]';
$amountSession      = $_SESSION['amount']           ?? 0;
$selected_months    = json_decode($selectedMonthsJson, true) ?: [];

// =========================
//  Get Access Token
// =========================
function getAccessToken($clientId, $clientSecret, $clientVersion, $baseUrl) {
    // Production token endpoint: /identity-manager/v1/oauth/token
    $url = $baseUrl . "/identity-manager/v1/oauth/token";
    $payload = http_build_query([
        'client_id'      => $clientId,
        'client_version' => $clientVersion,
        'client_secret'  => $clientSecret,
        'grant_type'     => 'client_credentials',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) throw new Exception("Token cURL error: " . $err);
    $data = json_decode($response, true);
    if (empty($data['access_token'])) throw new Exception("Token error: " . $response);
    return $data['access_token'];
}

// =========================
//  Check Order Status
// =========================
function getOrderStatus($accessToken, $merchantOrderId, $baseUrl) {
    $url = $baseUrl . "/checkout/v2/order/" . urlencode($merchantOrderId) . "/status";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken,
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) throw new Exception("Status cURL error: " . $err);
    return json_decode($response, true);
}

// =========================
//  Check Status & Process
// =========================
try {
    $token     = getAccessToken($clientId, $clientSecret, $clientVersion, $PHONEPE_BASE_OAUTH);
        $orderData = getOrderStatus($token, $merchantOrderId, $PHONEPE_BASE_PG);

    $state          = $orderData['state']       ?? 'UNKNOWN';
    $amountPaisa    = $orderData['amount']      ?? 0;
    $amountRupee    = $amountPaisa / 100;
    $phonepeOrderId = $orderData['orderId']     ?? '';

    // Extract payment details
    $paymentDetails = $orderData['paymentDetails'][0] ?? [];
    $transactionId  = $paymentDetails['transactionId'] ?? $merchantOrderId;
    $paymentMode    = $paymentDetails['paymentMode']   ?? '';
    $utr            = $paymentDetails['rail']['utr']              ?? '';
    $bankRef        = $paymentDetails['rail']['bankReferenceNumber'] ?? '';
    $vpa            = $paymentDetails['rail']['vpa']              ?? '';

    // =========================
    // Update pp_orders table
    // =========================
    $stmt = $conn->prepare("UPDATE pp_orders SET
        transaction_id=?, amount=?, state=?, payment_mode=?,
        gateway_order_id=?, gateway_response=?, payment_response=?,
        utr_number=?, bank_reference=?, vpa=?,
        payment_date=IF(?='COMPLETED', NOW(), payment_date),
        updated_at=NOW()
        WHERE merchant_order_id=?");

    $gatewayResponseJson = json_encode($orderData);
    $paymentResponseJson = json_encode($orderData);
    $stmt->bind_param("sdssssssssss",
        $transactionId, $amountRupee, $state, $paymentMode,
        $phonepeOrderId, $gatewayResponseJson, $paymentResponseJson,
        $utr, $bankRef, $vpa,
        $state, $merchantOrderId
    );
    $stmt->execute();

    // =========================
    // On SUCCESS: Insert DPS receipt & update student_fee
    // =========================
    if ($state === "COMPLETED" && $student_id && !empty($selected_months)) {

        $rdata = nmonth_fee_all($student_id, $selected_months);
        $rdata['previous_dues']      = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
        $rdata['paid_month']         = implode(",", $selected_months);
        $rdata['student_id']         = $student_id;
        $rdata['student_admission']  = get_data('student', $student_id, 'student_admission')['data'];
        $rdata['total']              = $amountRupee;
        $rdata['paid_amount']        = $amountRupee;
        $rdata['current_dues']       = 0;
        $rdata['remarks']            = 'PhonePe - ' . $transactionId;
        $rdata['other_fee']          = 0;
        $rdata['discount']           = 0;
        $rdata['paid_date']          = date('Y-m-d');
        $rdata['created_by']         = 'ONLINE';
        $rdata['payment_mode']       = 'BANK';

        $res = insert_data('receipt', $rdata);

        if ($res['status'] == 'success') {
            $rid = $res['id'];
            
            // Save receipt_id in pp_orders
            $stmtReceipt = $conn->prepare("UPDATE pp_orders SET receipt_id=? WHERE merchant_order_id=?");
            $stmtReceipt->bind_param("is", $rid, $merchantOrderId);
            $stmtReceipt->execute();

            foreach ($selected_months as $month) {
                $old_value = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
                $rid_val   = ($old_value != null && $month == 'other_month') ? $old_value . "," . $rid : $rid;
                update_data('student_fee', [remove_space($month) => $rid_val, 'current_dues' => 0], $student_id, 'student_id');
            }
        }

        // Redirect to app via deep link — Chrome Custom Tab will close automatically
        header("Location: dps://payment?status=success"
            . "&order_id=" . urlencode($merchantOrderId)
            . "&txn=" . urlencode($transactionId)
            . "&amount=" . $amountRupee
            . "&student_id=" . urlencode($student_id));
        exit();

    } elseif ($state === "FAILED" || $state === "PAYMENT_ERROR") {
        header("Location: dps://payment?status=failed&order_id=" . urlencode($merchantOrderId));
        exit();
    } else {
        // PENDING / UNKNOWN
        header("Location: dps://payment?status=pending&order_id=" . urlencode($merchantOrderId));
        exit();
    }

} catch (Exception $e) {
    echo "<div style='font-family:sans-serif;padding:40px;text-align:center;'>";
    echo "<h3 style='color:red;'>Error checking payment status</h3>";
    echo "<p style='color:#6b7280;margin:12px 0;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='payment_failed.php' style='padding:12px 28px;background:#dc2626;color:white;border-radius:8px;text-decoration:none;'>Go to Failed Page</a>";
    echo "</div>";
}
?>
