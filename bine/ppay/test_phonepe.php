<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$clientId      = "SU2509191800322669506083";
$clientSecret  = "5c5a0b98-69ee-4edc-a248-cc23a74b6288";
$clientVersion = 1;
// Correct production URLs (from SDK Constants.php):
$PHONEPE_BASE_OAUTH = "https://api.phonepe.com/apis";    // Token: /identity-manager/v1/oauth/token
$PHONEPE_BASE_PG    = "https://api.phonepe.com/apis/pg"; // Pay:   /checkout/v2/pay

echo "<pre style='font-family:monospace;padding:20px;'>";

// Step 1: Get access token
echo "=== STEP 1: Get Access Token ===\n";
$url = $PHONEPE_BASE_OAUTH . "/identity-manager/v1/oauth/token";
$payload = http_build_query([
    'client_id'      => $clientId,
    'client_version' => $clientVersion,
    'client_secret'  => $clientSecret,
    'grant_type'     => 'client_credentials',
]);

echo "URL: $url\n";
echo "Payload: $payload\n\n";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_VERBOSE        => false,
]);
$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "cURL Error: " . ($err ?: 'none') . "\n";
echo "Raw Response: $tokenResponse\n\n";

$tokenData = json_decode($tokenResponse, true);
echo "Parsed: " . print_r($tokenData, true) . "\n";

if (empty($tokenData['access_token'])) {
    echo "ERROR: Could not get access token!\n";
    echo "</pre>";
    exit;
}

$accessToken = $tokenData['access_token'];
echo "Access Token (first 30 chars): " . substr($accessToken, 0, 30) . "...\n\n";

// Step 2: Test initiate payment with ₹1
echo "=== STEP 2: Initiate Payment (₹1 test) ===\n";
$merchantOrderId = 'TEST_' . time();
$redirectUrl     = "https://dpsmushkipur.com/bine/ppay/response.php?merchantOrderId=" . $merchantOrderId;

$payPayload = json_encode([
    'merchantOrderId' => $merchantOrderId,
    'amount'          => 100,
    'expireAfter'     => 1200,
    'paymentFlow'     => [
        'type'         => 'PG_CHECKOUT',
        'message'      => 'Test Payment',
        'merchantUrls' => ['redirectUrl' => $redirectUrl]
    ]
]);

echo "URL: " . $PHONEPE_BASE_PG . "/checkout/v2/pay\n";
echo "Payload: $payPayload\n\n";

$ch = curl_init($PHONEPE_BASE_PG . "/checkout/v2/pay");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payPayload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: O-Bearer ' . $accessToken,
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$payResponse = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err2 = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode2\n";
echo "cURL Error: " . ($err2 ?: 'none') . "\n";
echo "Raw Response: $payResponse\n\n";

$payData = json_decode($payResponse, true);
echo "Parsed: " . print_r($payData, true) . "\n";

if (!empty($payData['redirectUrl'])) {
    echo "SUCCESS! Redirect URL: " . $payData['redirectUrl'] . "\n";
} else {
    echo "ERROR: No redirect URL in response!\n";
}

echo "</pre>";
?>
