<?php
// views/admin/expenses.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Expense Overview Header -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="content-card mb-0 bg-opacity-40">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total System Expenses</p>
            <h3 class="text-3xl font-extrabold text-red-400">Rs. <?php echo number_format($totalExpenses, 2); ?></h3>
            <p class="text-xs text-slate-500 mt-2">Sum of all teacher payouts, utility hosting, and infrastructure costs.</p>
        </div>

        <div class="content-card mb-0 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-white mb-1">Expense Log Management</h4>
                <p class="text-xs text-slate-400">Add, edit, or remove expense records to keep profit/loss calculations accurate.</p>
            </div>
            <button class="btn btn-primary btn-sm" data-open-modal="add-expense-modal">
                <i class="fa-solid fa-plus"></i>
                Add Expense Record
            </button>
        </div>
    </div>

    <!-- Expenses Ledger -->
    <div class="content-card">
        <div class="card-title">
            <span>Expenses Journal</span>
            <input type="text" class="form-control table-search max-w-xs" data-table="expenses-table" placeholder="Search expenses...">
        </div>

        <div class="table-responsive">
            <table class="table" id="expenses-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title / Description</th>
                        <th>Category</th>
                        <th>Expense Date</th>
                        <th>Amount (PKR)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-slate-400">No expense records logged in the database yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td>#EXP-00<?php echo $expense['expense_id']; ?></td>
                                <td>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($expense['title']); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($expense['description'] ?? 'No description'); ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo htmlspecialchars($expense['category']); ?></span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($expense['expense_date'])); ?>
                                </td>
                                <td class="font-semibold text-red-400">Rs. <?php echo number_format($expense['amount'], 2); ?></td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="btn btn-secondary btn-sm edit-expense-btn"
                                                data-id="<?php echo $expense['expense_id']; ?>"
                                                data-title="<?php echo htmlspecialchars($expense['title']); ?>"
                                                data-amount="<?php echo $expense['amount']; ?>"
                                                data-category="<?php echo htmlspecialchars($expense['category']); ?>"
                                                data-date="<?php echo $expense['expense_date']; ?>"
                                                data-desc="<?php echo htmlspecialchars($expense['description'] ?? ''); ?>">
                                            <i class="fa-solid fa-pen text-indigo-400"></i>
                                            Edit
                                        </button>
                                        <a href="<?php echo url('index.php?action=admin_expense_delete&id=' . $expense['expense_id']); ?>"
                                           class="btn btn-secondary btn-sm text-red-500 hover:bg-red-950"
                                           onclick="return confirm('Are you sure you want to delete this expense record?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Expense -->
<div class="modal" id="add-expense-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Expense Entry</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_expense_create'); ?>" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="new_exp_title" class="form-label">Expense Title</label>
                <input type="text" name="title" id="new_exp_title" class="form-control" placeholder="E.g. Web Hosting, Teacher Payroll June" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="new_exp_amount" class="form-label">Amount (PKR)</label>
                    <input type="number" name="amount" id="new_exp_amount" class="form-control" min="1" step="0.01" placeholder="5000" required>
                </div>
                <div class="form-group">
                    <label for="new_exp_category" class="form-label">Category</label>
                    <select name="category" id="new_exp_category" class="form-control form-select">
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Staff Payout">Staff Payout</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Utilities">Utilities</option>
                        <option value="General" selected>General</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="new_exp_date" class="form-label">Date Incurred</label>
                <input type="date" name="expense_date" id="new_exp_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="new_exp_desc" class="form-label">Description / Remarks</label>
                <textarea name="description" id="new_exp_desc" class="form-control" rows="3" placeholder="Additional details..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Log Expense</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Expense -->
<div class="modal" id="edit-expense-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Expense Entry</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_expense_edit'); ?>" method="POST" class="needs-validation">
            <input type="hidden" name="expense_id" id="edit_expense_id">

            <div class="form-group">
                <label for="edit_exp_title" class="form-label">Expense Title</label>
                <input type="text" name="title" id="edit_exp_title" class="form-control" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="edit_exp_amount" class="form-label">Amount (PKR)</label>
                    <input type="number" name="amount" id="edit_exp_amount" class="form-control" min="1" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="edit_exp_category" class="form-label">Category</label>
                    <select name="category" id="edit_exp_category" class="form-control form-select">
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Staff Payout">Staff Payout</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Utilities">Utilities</option>
                        <option value="General">General</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="edit_exp_date" class="form-label">Date Incurred</label>
                <input type="date" name="expense_date" id="edit_exp_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_exp_desc" class="form-label">Description / Remarks</label>
                <textarea name="description" id="edit_exp_desc" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Save Expense Changes</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtns = document.querySelectorAll(".edit-expense-btn");
    const editModal = document.getElementById("edit-expense-modal");

    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("edit_expense_id").value = btn.getAttribute("data-id");
            document.getElementById("edit_exp_title").value = btn.getAttribute("data-title");
            document.getElementById("edit_exp_amount").value = btn.getAttribute("data-amount");
            document.getElementById("edit_exp_category").value = btn.getAttribute("data-category");
            document.getElementById("edit_exp_date").value = btn.getAttribute("data-date");
            document.getElementById("edit_exp_desc").value = btn.getAttribute("data-desc");
            editModal.classList.add("active");
        });
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
