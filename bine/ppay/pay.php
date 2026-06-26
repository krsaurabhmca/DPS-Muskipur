<?php
session_start();

// Get parameters from URL
$student_id      = isset($_GET['student_id'])       ? htmlspecialchars($_GET['student_id'])       : '';
$amount          = isset($_GET['amount'])            ? htmlspecialchars($_GET['amount'])            : '';
$name            = isset($_GET['name'])              ? htmlspecialchars($_GET['name'])              : 'Student';
$phone           = isset($_GET['phone'])             ? htmlspecialchars($_GET['phone'])             : '';
$selected_months = isset($_GET['selected_months'])   ? $_GET['selected_months']                    : '[]';

$monthsArr = json_decode($selected_months, true) ?: [];

// Validate required parameters
if (empty($student_id) || empty($amount)) {
    die("Invalid payment request. Missing student_id or amount.");
}

// Generate unique order ID
$orderId = 'FEE' . $student_id . '_' . time();

// Store in session for verification
$_SESSION['payment_data'] = [
    'order_id'       => $orderId,
    'student_id'     => $student_id,
    'amount'         => $amount,
    'name'           => $name,
    'phone'          => $phone,
    'selected_months'=> $selected_months,
    'timestamp'      => time()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fees - DPS Mushkipur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .payment-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 860px;
            width: 100%;
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .payment-header i { font-size: 48px; margin-bottom: 12px; }
        .payment-header h1 { font-size: 26px; margin-bottom: 6px; }
        .payment-header p { opacity: 0.85; font-size: 14px; }
        .payment-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .payment-details {
            padding: 36px;
            background: #f8f9fa;
            border-right: 1px solid #e5e7eb;
        }
        .payment-form { padding: 36px; }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 15px;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-weight: 500; }
        .detail-value { color: #1f2937; font-weight: 600; text-align: right; }
        .months-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }
        .month-pill {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .amount-box {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-box .label { font-size: 13px; opacity: 0.85; margin-bottom: 4px; }
        .amount-box .amount { font-size: 38px; font-weight: 700; }
        h2 { color: #1f2937; margin-bottom: 22px; font-size: 22px; }
        .secure-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #10b981;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .secure-badge i { color: #10b981; font-size: 20px; }
        .secure-badge-text { font-size: 13px; color: #059669; }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(30,60,114,0.4); }
        .loading { display: none; text-align: center; padding: 20px; }
        .loading.active { display: block; }
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #1e3c72;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .terms { font-size: 13px; color: #6b7280; text-align: center; margin-top: 18px; }
        @media (max-width: 768px) {
            .payment-body { grid-template-columns: 1fr; }
            .payment-details { border-right: none; border-bottom: 1px solid #e5e7eb; }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <i class="fas fa-school"></i>
            <h1>DPS Mushkipur</h1>
            <p>Secure Fee Payment Gateway</p>
        </div>

        <div class="payment-body">
            <!-- Order Summary -->
            <div class="payment-details">
                <h2>Fee Summary</h2>

                <div class="detail-item">
                    <span class="detail-label">Student Name</span>
                    <span class="detail-value"><?php echo $name; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Student ID</span>
                    <span class="detail-value"><?php echo $student_id; ?></span>
                </div>

                <?php if (!empty($monthsArr)): ?>
                <div class="detail-item" style="flex-direction: column; align-items: flex-start;">
                    <span class="detail-label" style="margin-bottom:6px;">Selected Months</span>
                    <div class="months-list">
                        <?php foreach ($monthsArr as $month): ?>
                            <span class="month-pill"><?php echo htmlspecialchars(str_replace('_', ' ', $month)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="amount-box">
                    <div class="label">Total Amount</div>
                    <div class="amount">₹<?php echo number_format((float)$amount, 2); ?></div>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value" style="font-size:12px;"><?php echo $orderId; ?></span>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-form">
                <h2>Complete Payment</h2>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i>
                    <div class="secure-badge-text">
                        <strong>100% Secure Payment</strong><br>
                        Your information is encrypted & safe
                    </div>
                </div>

                <form id="paymentForm" action="process_payment.php" method="POST">
                    <input type="hidden" name="order_id"        value="<?php echo $orderId; ?>">
                    <input type="hidden" name="student_id"      value="<?php echo $student_id; ?>">
                    <input type="hidden" name="amount"          value="<?php echo $amount; ?>">
                    <input type="hidden" name="name"            value="<?php echo $name; ?>">
                    <input type="hidden" name="phone"           value="<?php echo $phone; ?>">
                    <input type="hidden" name="selected_months" value="<?php echo htmlspecialchars($selected_months); ?>">
                    <input type="hidden" name="payment_method"  value="ONLINE">

                    <p style="color:#6b7280; font-size:14px; margin-bottom:24px;">
                        You will be redirected to PhonePe to complete the payment securely.
                    </p>

                    <button type="submit" class="btn" id="payBtn">
                        <i class="fab fa-cc-visa"></i>
                        Pay ₹<?php echo number_format((float)$amount, 2); ?> via PhonePe
                    </button>

                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p>Redirecting to PhonePe...</p>
                    </div>

                    <div class="terms" style="margin-top:18px;">
                        By proceeding, you agree to DPS Mushkipur's fee payment terms.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('payBtn');
            const loading = document.getElementById('loading');
            btn.style.display = 'none';
            loading.classList.add('active');
            setTimeout(() => { this.submit(); }, 1000);
        });
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
</body>
</html>