<?php
// views/admin/reports.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <!-- Profit Loss Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="content-card mb-0 bg-opacity-40">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Gross Earnings</p>
            <h3 class="text-3xl font-extrabold text-emerald-400">Rs. <?php echo number_format($summary['total_earnings'], 2); ?></h3>
            <p class="text-xs text-slate-500 mt-2">Aggregated via JazzCash sandbox payments.</p>
        </div>
        
        <div class="content-card mb-0 bg-opacity-40">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">System Expenses</p>
            <h3 class="text-3xl font-extrabold text-red-400">Rs. <?php echo number_format($summary['simulated_expenses'], 2); ?></h3>
            <p class="text-xs text-slate-500 mt-2">Approximated hosting and infrastructure payout.</p>
        </div>

        <div class="content-card mb-0 bg-gradient-to-br from-indigo-900 to-transparent bg-opacity-30">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Net System Profit</p>
            <h3 class="text-3xl font-extrabold text-white">Rs. <?php echo number_format($summary['net_profit'], 2); ?></h3>
            <p class="text-xs text-slate-400 mt-2 font-medium">Reconciled profit margins.</p>
        </div>
    </div>

    <!-- Payment Logs Table & Export Buttons -->
    <div class="content-card">
        <div class="card-title">
            <span>Financial Ledger</span>
            <div class="flex items-center gap-2">
                <a href="<?php echo url('index.php?action=admin_reports_export_csv'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-file-csv text-emerald-400"></i>
                    Export CSV
                </a>
                <a href="<?php echo url('index.php?action=admin_reports_export_pdf'); ?>" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-file-pdf"></i>
                    Print / Export PDF
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Student Name & Reg No</th>
                        <th>Parent Client</th>
                        <th>JazzCash Transaction ID</th>
                        <th>Amount Paid</th>
                        <th>Received Date</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-slate-400 py-8">No payments logged in the database.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>#INV-00<?php echo $payment['payment_id']; ?></td>
                                <td>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($payment['student_name']); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($payment['registration_no']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($payment['parent_name']); ?></td>
                                <td>
                                    <span class="font-mono text-sm">
                                        <?php echo $payment['jazzcash_trx_id'] ? htmlspecialchars($payment['jazzcash_trx_id']) : '<em class="text-slate-500">N/A</em>'; ?>
                                    </span>
                                </td>
                                <td class="font-semibold text-emerald-400">Rs. <?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo $payment['payment_date'] ? date('M d, Y h:i A', strtotime($payment['payment_date'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($payment['status'] === 'Paid'): ?>
                                        <span class="badge badge-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
