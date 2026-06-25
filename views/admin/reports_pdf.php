<?php
// views/admin/reports_pdf.php
require_once __DIR__ . '/../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPTMS Financial Statement</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        .summary-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary-card h4 {
            margin: 0 0 5px 0;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
        }
        .summary-card p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
        }
        .no-print {
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">Print This Report</button>

    <div class="header">
        <div class="brand">Online Parent-Teacher Meeting System</div>
        <div class="title">
            Financial Audit Statement<br>
            <span style="font-size: 12px; font-weight: normal; color: #64748b;">Generated on: <?php echo date('Y-m-d H:i:s'); ?></span>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-card">
            <h4>Total Gross Earnings</h4>
            <p>PKR <?php echo number_format($summary['total_earnings'], 2); ?></p>
        </div>
        <div class="summary-card">
            <h4>Simulated Expenses</h4>
            <p style="color: #ef4444;">PKR <?php echo number_format($summary['simulated_expenses'], 2); ?></p>
        </div>
        <div class="summary-card" style="background-color: #ecfdf5; border-color: #a7f3d0;">
            <h4>Net Profit Margin</h4>
            <p style="color: #10b981;">PKR <?php echo number_format($summary['net_profit'], 2); ?></p>
        </div>
    </div>

    <h3>Transaction Audits</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Student Name</th>
                <th>Registration No</th>
                <th>Parent Client</th>
                <th>JazzCash Trx ID</th>
                <th>Amount Received</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td>#INV-00<?php echo $payment['payment_id']; ?></td>
                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($payment['registration_no']); ?></td>
                    <td><?php echo htmlspecialchars($payment['parent_name']); ?></td>
                    <td><?php echo $payment['jazzcash_trx_id'] ?: 'N/A'; ?></td>
                    <td>PKR <?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo $payment['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        © <?php echo date('Y'); ?> OPTMS Project Registry - BC250219905 & BC230212937. All rights reserved.
    </div>

    <!-- Automatically open browser print window -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
