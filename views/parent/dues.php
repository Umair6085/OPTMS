<?php
// views/parent/dues.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (isset($_SESSION['payment_success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($_SESSION['payment_success']); unset($_SESSION['payment_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['booking_error'])): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-lock mr-2"></i>
            <?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Gatekeeper warning banner -->
    <?php 
    require_once __DIR__ . '/../../models/Settings.php';
    $enforceDues = Settings::get('restrict_dues_booking', '1') === '1';
    if ($enforceDues && Payment::hasUnpaidDues($_SESSION['user_id'])): 
    ?>
        <div class="alert alert-warning mb-6">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-amber-500 text-lg"></i>
                <div>
                    <h4 class="font-bold">Financial Gatekeeper Active</h4>
                    <p class="text-xs text-amber-200">PTM Meeting Scheduler is currently locked. Complete outstanding dues below to restore instant scheduler access.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="content-card">
        <div class="card-title">
            <span>Verify School Dues Ledger</span>
            <span class="text-sm font-semibold text-slate-400">View and complete outstanding dues for registered students.</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Student Profile</th>
                        <th>School Registration ID</th>
                        <th>Payment Dues Amount</th>
                        <th>Transaction Ref</th>
                        <th>Payment Date</th>
                        <th>Invoice Status</th>
                        <th>Gateway Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dues)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-slate-400 py-8">
                                <i class="fa-solid fa-file-invoice text-4xl text-slate-600 block mb-2"></i>
                                No invoices generated under your account. Register a student first.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dues as $invoice): ?>
                            <tr>
                                <td>#INV-00<?php echo $invoice['payment_id']; ?></td>
                                <td class="font-semibold text-white"><?php echo htmlspecialchars($invoice['student_name']); ?></td>
                                <td class="font-mono text-xs"><?php echo htmlspecialchars($invoice['registration_no']); ?></td>
                                <td class="font-semibold text-slate-200">Rs. <?php echo number_format($invoice['amount'], 2); ?></td>
                                <td>
                                    <span class="font-mono text-xs">
                                        <?php echo $invoice['jazzcash_trx_id'] ? htmlspecialchars($invoice['jazzcash_trx_id']) : '<em class="text-slate-500">Pending</em>'; ?>
                                    </span>
                                </td>
                                <td><?php echo $invoice['payment_date'] ? date('M d, Y h:i A', strtotime($invoice['payment_date'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($invoice['status'] === 'Paid'): ?>
                                        <span class="badge badge-success">Dues Cleared</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($invoice['status'] === 'Unpaid'): ?>
                                        <a href="<?php echo url('index.php?action=jazzcash_sandbox&payment_id=' . $invoice['payment_id']); ?>" 
                                           class="btn btn-primary btn-sm flex items-center justify-center gap-1 bg-red-600 hover:bg-red-700">
                                            <i class="fa-solid fa-wallet text-amber-400"></i>
                                            Pay Now (JazzCash)
                                        </a>
                                    <?php else: ?>
                                        <span class="text-emerald-400 text-xs font-semibold">
                                            <i class="fa-solid fa-circle-check mr-1"></i>
                                            Paid via Sandbox
                                        </span>
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
