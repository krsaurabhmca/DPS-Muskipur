<?php
session_start();
require_once 'db_connect.php';

$orderId    = isset($_GET['order_id'])    ? $_GET['order_id']    : '';
$student_id = isset($_GET['student_id']) ? $_GET['student_id']  : '';
$amount     = isset($_GET['amount'])     ? $_GET['amount']       : '0';
$name       = isset($_GET['name'])       ? $_GET['name']         : 'Student';
$txn        = isset($_GET['txn'])        ? $_GET['txn']          : '';

// Try to get payment record from pp_orders
$payment = null;
if (!empty($orderId)) {
    $stmt = $conn->prepare("SELECT * FROM pp_orders WHERE merchant_order_id = ? LIMIT 1");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
}

// Try to get student info
$student = null;
if (!empty($student_id)) {
    $stmt = $conn->prepare("SELECT student_name, student_class, student_section, student_admission, student_mobile FROM student WHERE id = ? LIMIT 1");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}

$displayName   = $student['student_name'] ?? $name;
$displayClass  = isset($student['student_class']) ? 'Class ' . $student['student_class'] . '-' . $student['student_section'] : '';
$displayAmount = $payment['amount'] ?? $amount;
$displayTxn    = $txn ?: ($payment['transaction_id'] ?? $orderId);
$paymentDate   = $payment['payment_date'] ?? date('Y-m-d H:i:s');
$paymentMode   = $payment['payment_mode'] ?? 'PHONEPE';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - DPS Mushkipur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 640px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .success-header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            width: 100px; height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.6s ease-out 0.2s backwards;
        }
        .success-icon i { font-size: 50px; color: #10b981; }
        @keyframes scaleIn {
            0%   { transform: scale(0); }
            50%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .success-header h1 { font-size: 30px; margin-bottom: 8px; }
        .success-header p { opacity: 0.9; font-size: 15px; }
        .status-badge {
            display: inline-block;
            background: white;
            color: #10b981;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 14px;
            font-size: 14px;
        }
        .success-body { padding: 36px; }
        .amount-box {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 28px;
            box-shadow: 0 8px 24px rgba(16,185,129,0.3);
        }
        .amount-box .label { font-size: 13px; opacity: 0.85; margin-bottom: 4px; }
        .amount-box .amount { font-size: 42px; font-weight: 700; }
        .details-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-weight: 500; }
        .detail-value { color: #1f2937; font-weight: 600; text-align: right; word-break: break-all; max-width: 60%; }
        .action-buttons { display: flex; gap: 14px; margin-top: 24px; }
        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30,60,114,0.4); }
        .btn-outline { background: white; color: #1e3c72; border: 2px solid #1e3c72; }
        .btn-outline:hover { background: #f0f4ff; }
        .footer-note {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
        }
        @media (max-width: 480px) {
            .action-buttons { flex-direction: column; }
            .success-body { padding: 24px 18px; }
        }
        @media print {
            body { background: white; }
            .action-buttons { display: none; }
            .success-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Payment Successful!</h1>
            <p>Fee payment has been recorded</p>
            <span class="status-badge">
                <i class="fas fa-check-circle"></i> COMPLETED
            </span>
        </div>

        <div class="success-body">
            <div class="amount-box">
                <div class="label">Amount Paid</div>
                <div class="amount">₹<?php echo number_format((float)$displayAmount, 2); ?></div>
            </div>

            <div class="details-card">
                <div class="section-title"><i class="fas fa-user-graduate"></i> Student Details</div>
                <div class="detail-row">
                    <span class="detail-label">Student Name</span>
                    <span class="detail-value"><?php echo htmlspecialchars($displayName); ?></span>
                </div>
                <?php if ($displayClass): ?>
                <div class="detail-row">
                    <span class="detail-label">Class</span>
                    <span class="detail-value"><?php echo htmlspecialchars($displayClass); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($student['student_admission'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Admission No.</span>
                    <span class="detail-value"><?php echo htmlspecialchars($student['student_admission']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="details-card">
                <div class="section-title"><i class="fas fa-receipt"></i> Transaction Details</div>
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value" style="font-size:12px;"><?php echo htmlspecialchars($orderId); ?></span>
                </div>
                <?php if ($displayTxn && $displayTxn !== $orderId): ?>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID</span>
                    <span class="detail-value" style="font-size:12px;"><?php echo htmlspecialchars($displayTxn); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Payment Mode</span>
                    <span class="detail-value"><?php echo htmlspecialchars(str_replace('_', ' ', $paymentMode)); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?php echo date('d M Y, h:i A', strtotime($paymentDate)); ?></span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="download_receipt.php?merchant_order_id=<?php echo urlencode($orderId); ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i> Download Receipt
                </a>
                <a href="javascript:window.close()" class="btn btn-outline">
                    <i class="fas fa-times"></i> Close
                </a>
            </div>

            <div class="footer-note">
                <p>For any queries, contact DPS Mushkipur:</p>
                <p><strong>Phone:</strong> School Office</p>
            </div>
        </div>
    </div>

    <script>
        // Prevent back navigation after successful payment
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
</body>
</html>