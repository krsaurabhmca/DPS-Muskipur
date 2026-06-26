<?php
$reason  = isset($_GET['reason'])   ? htmlspecialchars($_GET['reason'])   : '';
$orderId = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';

$errorMessages = [
    'payment_failed' => 'Your payment was declined by the bank. Please try a different payment method.',
    'pending'        => 'Your payment is still being processed. Please check back in a few minutes.',
    'key_not_found'  => 'Payment gateway configuration error. Please contact the school office.',
    ''               => 'Payment could not be completed. Please try again or contact support.',
];
$errorText = $errorMessages[$reason] ?? $errorMessages[''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - DPS Mushkipur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .failed-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .failed-header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .failed-icon {
            width: 90px; height: 90px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .failed-icon i { font-size: 44px; color: white; }
        .failed-header h1 { font-size: 26px; margin-bottom: 8px; }
        .failed-header p { opacity: 0.85; font-size: 14px; }
        .failed-body { padding: 36px; text-align: center; }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-left: 4px solid #ef4444;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 24px;
            text-align: left;
        }
        .error-box i { color: #ef4444; margin-right: 8px; }
        .error-box p { color: #dc2626; font-size: 14px; line-height: 1.6; }
        <?php if ($orderId): ?>
        .order-id {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 18px;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .order-id strong { color: #374151; }
        <?php endif; ?>
        .info-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 28px;
            line-height: 1.6;
        }
        .action-buttons { display: flex; gap: 12px; }
        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
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
        .btn-outline { background: white; color: #ef4444; border: 2px solid #ef4444; }
        .btn-outline:hover { background: #fef2f2; }
        @media (max-width: 400px) { .action-buttons { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="failed-container">
        <div class="failed-header">
            <div class="failed-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1>Payment Failed</h1>
            <p>We couldn't process your payment</p>
        </div>

        <div class="failed-body">
            <div class="error-box">
                <p><i class="fas fa-exclamation-circle"></i><?php echo $errorText; ?></p>
            </div>

            <?php if ($orderId): ?>
            <div class="order-id">
                <strong>Order ID:</strong> <?php echo $orderId; ?>
            </div>
            <?php endif; ?>

            <p class="info-text">
                No amount has been deducted. Please try again or visit the school office for assistance.
            </p>

            <div class="action-buttons">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Try Again
                </a>
                <a href="javascript:window.close()" class="btn btn-outline">
                    <i class="fas fa-times"></i> Close
                </a>
            </div>
        </div>
    </div>
</body>
</html>