<?php
require_once 'db_connect.php';

// Read merchant_order_id from URL
$merchantOrderId = isset($_GET['merchant_order_id']) ? $_GET['merchant_order_id'] : '';

if (empty($merchantOrderId)) {
    die("Invalid receipt request");
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = $db->prepare("
        SELECT 
            p.*, 
            a.first_name, a.last_name, a.email, a.phone,
            a.application_id,
            c.course_name, c.course_fee
        FROM pp_orders p
        LEFT JOIN applications a ON p.application_id = a.application_id
        LEFT JOIN courses c ON a.course_id = c.id
        WHERE p.merchant_order_id = ?
    ");
    $stmt->execute([$merchantOrderId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        die("Receipt not found");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?= htmlspecialchars($data['merchant_order_id']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        /* A4 Page */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 10mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .logo-section img {
            height: 80px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
            max-width: 300px;
        }

        .receipt-info {
            text-align: right;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }

        .receipt-no {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .receipt-date {
            font-size: 11px;
            color: #666;
        }

        /* Status Badge */
        .status {
            display: inline-block;
            padding: 5px 15px;
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid;
        }

        .status.paid {
            color: #059669;
            border-color: #059669;
            background: #d1fae5;
        }

        .status.failed {
            color: #dc2626;
            border-color: #dc2626;
            background: #fee2e2;
        }

        /* Section */
        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        table td {
            padding: 6px 0;
            font-size: 13px;
            vertical-align: top;
        }

        table td:first-child {
            color: #666;
            width: 35%;
            font-weight: 500;
        }

        table td:last-child {
            color: #000;
            font-weight: 600;
        }

        /* Payment Summary */
        .summary-box {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 10px;
            margin: 20px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #000;
            font-size: 18px;
            font-weight: bold;
        }

        /* Amount in Words */
        .amount-words {
            background: #fff;
            border: 1px dashed #ccc;
            padding: 12px;
            margin: 20px 0;
            font-size: 12px;
            font-style: italic;
            color: #666;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 10px;
        }

        .signature-block {
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            margin-bottom: 8px;
        }

        .signature-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #999;
        }

        .footer p {
            margin: 5px 0;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #000;
            color: white;
            border: none;
            padding: 15px 35px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }

        .print-button:hover {
            background: #333;
            transform: translateY(-2px);
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .page {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }

            .print-button {
                display: none;
            }

            .section,
            table,
            .summary-box {
                page-break-inside: avoid;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .page {
                width: 100%;
                padding: 20px;
            }

            .header {
                flex-direction: column;
            }

            .receipt-info {
                text-align: left;
                margin-top: 20px;
            }

            .signatures {
                flex-direction: column;
                gap: 40px;
            }

            .signature-block {
                width: 100%;
            }

            .print-button {
                position: static;
                width: 100%;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="Logo">
            <div class="company-name"><?= htmlspecialchars($settingsData['site_name']) ?></div>
            <div class="company-address">
                <?= htmlspecialchars($settingsData['site_address']) ?><br>
                Phone: <?= htmlspecialchars($settingsData['site_phone']) ?><br>
                Email: <?= htmlspecialchars($settingsData['site_email']) ?>
            </div>
        </div>
        <div class="receipt-info">
            <div class="receipt-title">RECEIPT</div>
            <div class="receipt-no">Receipt No: <?= htmlspecialchars($data['merchant_order_id']) ?></div>
            <div class="receipt-date">
                Date: <?php 
                echo !empty($data['payment_date']) 
                    ? date('d-m-Y', strtotime($data['payment_date'])) 
                    : date('d-m-Y'); 
                ?>
            </div>
            <div class="status <?= $data['state'] === 'COMPLETED' ? 'paid' : 'failed' ?>">
                <?= $data['state'] === 'COMPLETED' ? 'PAID' : 'FAILED' ?>
            </div>
        </div>
    </div>

    <!-- Received From -->
    <div class="section">
        <div class="section-title">Received From</div>
        <table>
            <tr>
                <td>Name</td>
                <td><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= htmlspecialchars($data['email']) ?></td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td><?= htmlspecialchars($data['customer_mobile']) ?></td>
            </tr>
            <tr>
                <td>Application ID</td>
                <td><?= htmlspecialchars($data['application_id']) ?></td>
            </tr>
        </table>
    </div>

    <!-- Payment Details -->
    <div class="section">
        <div class="section-title">Payment Details</div>
        <table>
            <tr>
                <td>Course Name</td>
                <td><?= htmlspecialchars($data['course_name']) ?></td>
            </tr>
            <tr>
                <td>Transaction ID</td>
                <td><?= htmlspecialchars($data['transaction_id']) ?></td>
            </tr>
            <tr>
                <td>PhonePe Order ID</td>
                <td><?= htmlspecialchars($data['gateway_order_id']) ?></td>
            </tr>
            <tr>
                <td>UTR Number</td>
                <td><?= !empty($data['utr_number']) ? htmlspecialchars($data['utr_number']) : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td><?= strtoupper(htmlspecialchars($data['payment_mode'])) ?></td>
            </tr>
            <tr>
                <td>Payment Date & Time</td>
                <td>
                  
                   <?php
                        date_default_timezone_set('Asia/Kolkata'); // Indian Time
                        
                        echo !empty($data['payment_date']) 
                            ? date('d F Y, h:i A', strtotime($data['payment_date'] . ' +5 hours 30 minutes'))
                            : "N/A";
                        ?>


                </td>
            </tr>
        </table>
    </div>

    <!-- Payment Summary -->
    <div class="summary-box">
        <div class="summary-row">
            <span>Course Fee</span>
            <span>₹<?= number_format($data['amount'], 2) ?></span>
        </div>
        <div class="summary-row">
            <span>Tax & Charges</span>
            <span>₹0.00</span>
        </div>
        <div class="summary-row total">
            <span>TOTAL AMOUNT</span>
            <span>₹<?= number_format($data['amount'], 2) ?></span>
        </div>
    </div>

    <!-- Amount in Words -->
    <div class="amount-words">
        Amount in words: <strong><?php
        $amount = $data['amount'];
        $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
        echo ucwords($formatter->format($amount)) . ' Rupees Only';
        ?></strong>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Student Signature</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <!--<p>For any queries, please contact us at <?= htmlspecialchars($settingsData['site_email']) ?></p>-->
        <p style="margin-top: 10px;">Thank you for your payment!</p>
    </div>
</div>

<!-- Print Button -->
<button class="print-button" onclick="window.print()">PRINT RECEIPT</button>

</body>
</html>