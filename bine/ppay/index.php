<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// =========================
//  PhonePe Credentials
// =========================
$clientId      = "SU2509191800322669506083";
$clientSecret  = "5c5a0b98-69ee-4edc-a248-cc23a74b6288";
$clientVersion = 1;
// Use PRODUCTION endpoint; for sandbox use: https://api-preprod.phonepe.com/apis/pg-sandbox
// Production base URLs (from PhonePe SDK Constants.php)
$PHONEPE_BASE_OAUTH = "https://api.phonepe.com/apis";       // for OAuth token
$PHONEPE_BASE_PG    = "https://api.phonepe.com/apis/pg";    // for pay & status

// =========================
//  GET REQUEST VALUES
// =========================
$student_id      = $_GET['student_id']      ?? '';
$amountRupee     = $_GET['amount']          ?? 1;
$customerName    = $_GET['name']            ?? '';
$customerMobile  = $_GET['phone']           ?? '';
$selectedMonths  = $_GET['selected_months'] ?? '[]';

if (!$student_id || !$amountRupee) {
    die("Missing required parameters: student_id and amount are required.");
}

// Unique merchant order ID
$merchantOrderId = 'FEE' . $student_id . '_' . time();

// Store in session for response.php
$_SESSION['merchantOrderId']  = $merchantOrderId;
$_SESSION['student_id']       = $student_id;
$_SESSION['selected_months']  = $selectedMonths;
$_SESSION['amount']           = $amountRupee;

$amountPaisa = (int)round($amountRupee * 100);
$redirectUrl = "https://dpsmushkipur.com/bine/ppay/response.php?merchantOrderId=" . urlencode($merchantOrderId);

// =========================
//  Get Access Token
// =========================
function getAccessToken($clientId, $clientSecret, $clientVersion, $baseUrl) {
    // Production token endpoint: /identity-manager/v1/oauth/token
    $url = $baseUrl . "/identity-manager/v1/oauth/token";
    $payload = http_build_query([
        'client_id'     => $clientId,
        'client_version'=> $clientVersion,
        'client_secret' => $clientSecret,
        'grant_type'    => 'client_credentials',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        throw new Exception("Token request cURL error: " . $err);
    }

    $data = json_decode($response, true);
    if (empty($data['access_token'])) {
        throw new Exception("Token error: " . $response);
    }
    return $data['access_token'];
}

// =========================
//  Initiate Payment
// =========================
function initiatePayment($accessToken, $merchantOrderId, $amountPaisa, $redirectUrl, $baseUrl) {
    $url = $baseUrl . "/checkout/v2/pay";

    $payload = json_encode([
        'merchantOrderId' => $merchantOrderId,
        'amount'          => $amountPaisa,
        'expireAfter'     => 1200,
        'paymentFlow'     => [
            'type'        => 'PG_CHECKOUT',
            'message'     => 'School Fee Payment',
            'merchantUrls'=> [
                'redirectUrl' => $redirectUrl
            ]
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
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

    if ($err) {
        throw new Exception("Payment cURL error: " . $err);
    }

    return json_decode($response, true);
}

// =========================
//  Store in DB (pp_orders)
// =========================
require_once 'db_connect.php';

$sql = "INSERT INTO pp_orders 
(merchant_order_id, amount, currency, state, payment_mode, payment_gateway,
 customer_name, customer_email, customer_mobile, application_id, course_code,
 payload, payment_data, ip_address, user_agent, created_at, updated_at)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";

$stmt    = $conn->prepare($sql);
$currency = "INR"; $state = "INITIATED"; $paymentMode = "";
$payGW   = "PHONEPE"; $customerEmail = ""; $courseCode = "FEE";
$ip      = $_SERVER['REMOTE_ADDR'];
$ua      = $_SERVER['HTTP_USER_AGENT'];
$payloadStr = json_encode(['amount'=>$amountPaisa,'merchantOrderId'=>$merchantOrderId]);
$paymentDataStr = json_encode($_GET);

$stmt->bind_param("sdsssssssssssss",
    $merchantOrderId, $amountRupee, $currency, $state, $paymentMode, $payGW,
    $customerName, $customerEmail, $customerMobile,
    $student_id, $courseCode, $payloadStr, $paymentDataStr, $ip, $ua
);
$stmt->execute();

// =========================
//  Execute Payment
// =========================
try {
    $token   = getAccessToken($clientId, $clientSecret, $clientVersion, $PHONEPE_BASE_OAUTH);
    $payResp = initiatePayment($token, $merchantOrderId, $amountPaisa, $redirectUrl, $PHONEPE_BASE_PG);

    if (!empty($payResp['redirectUrl'])) {
        // Update DB with gateway order id
        $gwOrderId = $payResp['orderId'] ?? '';
        $conn->query("UPDATE pp_orders SET gateway_order_id='$gwOrderId', state='PENDING', updated_at=NOW() WHERE merchant_order_id='$merchantOrderId'");

        // Redirect to PhonePe checkout
        header("Location: " . $payResp['redirectUrl']);
        exit();
    } else {
        $errMsg = $payResp['message'] ?? json_encode($payResp);
        throw new Exception("PhonePe did not return a redirect URL: " . $errMsg);
    }

} catch (Exception $e) {
    // Update DB to failed
    $conn->query("UPDATE pp_orders SET state='FAILED', updated_at=NOW() WHERE merchant_order_id='$merchantOrderId'");
    echo "<div style='font-family:sans-serif;padding:40px;text-align:center;'>";
    echo "<h2 style='color:#dc2626;'>Payment Gateway Error</h2>";
    echo "<p style='color:#6b7280;margin:16px 0;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='javascript:history.back()' style='padding:12px 28px;background:#1e3c72;color:white;border-radius:8px;text-decoration:none;'>Go Back</a>";
    echo "</div>";
}
?>