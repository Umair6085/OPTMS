<?php
// views/parent/jazzcash_sandbox.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in py-6">
    <div class="jazzcash-frame">
        <div class="jazzcash-header">
            <div class="jazzcash-logo">
                Jazz<span>Cash</span>
            </div>
            <span class="text-xs font-mono bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded font-bold">SANDBOX</span>
        </div>

        <h3 class="text-lg font-bold text-slate-800 mb-4">Complete Payment</h3>
        <p class="text-xs text-slate-500 mb-6">Select a payment option and enter details to simulate a real JazzCash Sandbox checkout transaction.</p>

        <!-- Invoice Details Box -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-slate-700">
            <div class="jazzcash-details-row">
                <span class="text-slate-500">Student Profile</span>
                <strong class="text-slate-800">INV-00<?php echo $payment['payment_id']; ?></strong>
            </div>
            <div class="jazzcash-details-row">
                <span class="text-slate-500">Merchant Name</span>
                <strong class="text-slate-800">OPTMS School Administration</strong>
            </div>
            <div class="jazzcash-details-row total">
                <span class="text-slate-900">Total Dues Amount</span>
                <strong class="text-red-600">Rs. <?php echo number_format($payment['amount'], 2); ?> PKR</strong>
            </div>
        </div>

        <!-- Payment Method Tabs -->
        <div class="flex border-b border-slate-200 mb-6">
            <button class="flex-1 py-2 text-center text-sm font-semibold border-b-2 border-red-600 text-red-600">Mobile Wallet Account</button>
            <button class="flex-1 py-2 text-center text-sm font-semibold text-slate-400 cursor-not-allowed" disabled>Credit / Debit Card</button>
        </div>

        <!-- Checkout Form -->
        <form action="<?php echo url('index.php?action=jazzcash_sandbox&payment_id=' . $payment['payment_id']); ?>" method="POST" class="needs-validation">
            <div class="form-group mb-4">
                <label for="mobile_no" class="block text-xs font-bold text-slate-500 uppercase mb-1">JazzCash Mobile Number</label>
                <input type="text" name="mobile_no" id="mobile_no" class="w-100 border border-slate-300 rounded-lg p-2.5 text-slate-800 text-sm focus:outline-none focus:border-red-600" placeholder="E.g. 03001234567" required>
            </div>

            <div class="form-group mb-6">
                <label for="mpin" class="block text-xs font-bold text-slate-500 uppercase mb-1">Simulated Wallet MPIN</label>
                <input type="password" name="mpin" id="mpin" class="w-100 border border-slate-300 rounded-lg p-2.5 text-slate-800 text-sm focus:outline-none focus:border-red-600" placeholder="••••" maxlength="4" required>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?php echo url('index.php?action=parent_dues'); ?>" class="flex-1 text-center text-sm font-semibold text-slate-500 hover:text-slate-700 py-3 border border-slate-300 rounded-lg">Cancel Transaction</a>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg text-sm shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-lock text-yellow-400"></i>
                    Pay Dues Securely
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
