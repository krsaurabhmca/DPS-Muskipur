<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Get form data
$orderId         = $_POST['order_id'] ?? '';
$student_id      = $_POST['student_id'] ?? '';
$amount          = $_POST['amount'] ?? 0;
$name            = $_POST['name'] ?? '';
$phone           = $_POST['phone'] ?? '';
$selectedMonths  = $_POST['selected_months'] ?? '[]';
$paymentMethod   = $_POST['payment_method'] ?? 'ONLINE';

// Verify session data
if (!isset($_SESSION['payment_data']) || $_SESSION['payment_data']['order_id'] !== $orderId) {
    die("Invalid payment session. Please go back and try again.");
}

$monthsArr = json_decode($selectedMonths, true) ?: [];

// -----------------------------------------------
// Include DPS library to insert receipt properly
// -----------------------------------------------
require_once __DIR__ . '/../required/op_lib.php';

try {
    // Build receipt data exactly like the manual pay_fee API task
    $rdata = nmonth_fee_all($student_id, $monthsArr);
    $rdata['previous_dues']     = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
    $rdata['paid_month']        = implode(",", $monthsArr);
    $rdata['student_id']        = $student_id;
    $rdata['student_admission'] = get_data('student', $student_id, 'student_admission')['data'];
    $rdata['total']             = $amount;
    $rdata['paid_amount']       = $amount;
    $rdata['current_dues']      = 0;
    $rdata['remarks']           = 'PhonePe Online Payment - Order: ' . $orderId;
    $rdata['other_fee']         = 0;
    $rdata['discount']          = 0;
    $rdata['paid_date']         = date('Y-m-d');
    $rdata['created_by']        = 'ONLINE';
    $rdata['payment_mode']      = 'BANK';

    $res = insert_data('receipt', $rdata);

    if ($res['status'] == 'success') {
        $rid = $res['id'];
        foreach ($monthsArr as $month) {
            $old_value = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
            if ($old_value != null && $month == 'other_month') {
                $rid_val = $old_value . "," . $rid;
            } else {
                $rid_val = $rid;
            }
            update_data('student_fee', [remove_space($month) => $rid_val, 'current_dues' => 0], $student_id, 'student_id');
        }

        // Redirect to success page
        header("Location: payment_success.php?order_id=" . urlencode($orderId) . "&student_id=" . urlencode($student_id) . "&amount=" . urlencode($amount) . "&name=" . urlencode($name));
        exit();
    } else {
        throw new Exception("Receipt insertion failed: " . ($res['message'] ?? 'Unknown error'));
    }

} catch (Exception $e) {
    error_log("Process Payment Error: " . $e->getMessage());
    header("Location: payment_failed.php?reason=" . urlencode($e->getMessage()));
    exit();
}
?>