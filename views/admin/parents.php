<?php
// views/admin/parents.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="content-card">
        <div class="card-title">
            <span>Pending Parent Approvals</span>
            <span class="text-sm font-semibold text-slate-400">Verifying registrations based on student enrollment.</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Parent ID</th>
                        <th>Parent Name & Email</th>
                        <th>Student Name</th>
                        <th>Student Reg No</th>
                        <th>Class / Grade</th>
                        <th>DOB</th>
                        <th>Registration Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingParents)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-slate-400 py-8">
                                <i class="fa-solid fa-user-shield text-4xl text-slate-600 block mb-2"></i>
                                No parent accounts pending verification.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingParents as $parent): ?>
                            <tr>
                                <td>#<?php echo $parent['user_id']; ?></td>
                                <td>
                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($parent['name']); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($parent['email']); ?></div>
                                </td>
                                <td class="font-semibold text-indigo-300"><?php echo htmlspecialchars($parent['student_name'] ?? 'N/A'); ?></td>
                                <td><span class="font-mono text-slate-300"><?php echo htmlspecialchars($parent['registration_no'] ?? 'N/A'); ?></span></td>
                                <td><?php echo htmlspecialchars($parent['class_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($parent['dob'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge badge-warning"><?php echo $parent['status']; ?></span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo url('index.php?action=admin_parent_approve&id=' . $parent['user_id']); ?>" 
                                           class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i>
                                            Approve
                                        </a>
                                        <a href="<?php echo url('index.php?action=admin_parent_reject&id=' . $parent['user_id']); ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to REJECT and delete this parent registration?');">
                                            <i class="fa-solid fa-xmark"></i>
                                            Reject
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

<?php include __DIR__ . '/../layout/footer.php'; ?>
