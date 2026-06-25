<?php
// views/teacher/feedback.php
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
            <span>Parent Reviews & Post-Meeting Feedback</span>
            <span class="text-sm font-semibold text-slate-400">Feedback submitted immediately after sessions conclude.</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Meeting Date</th>
                        <th>Parent Client</th>
                        <th>Rating Stars</th>
                        <th>Parent Comments</th>
                        <th>Admin Flag</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-slate-400 py-8">
                                <i class="fa-solid fa-comments text-4xl text-slate-600 block mb-2"></i>
                                No parent feedback reviews submitted yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td>#FB-00<?php echo $feedback['feedback_id']; ?></td>
                                <td class="font-semibold text-white">
                                    <?php echo date('M d, Y', strtotime($feedback['slot_time'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($feedback['parent_name']); ?></td>
                                <td>
                                    <!-- Stars Renderer -->
                                    <div class="text-amber-400 text-sm flex gap-0.5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $feedback['rating_stars']): ?>
                                                <i class="fa-solid fa-star"></i>
                                            <?php else: ?>
                                                <i class="fa-regular fa-star text-slate-600"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td class="text-slate-300 max-w-xs truncate" title="<?php echo htmlspecialchars($feedback['comments']); ?>">
                                    <?php echo htmlspecialchars($feedback['comments']); ?>
                                </td>
                                <td>
                                    <?php if ($feedback['needs_admin_action']): ?>
                                        <span class="badge badge-danger">Escalated to Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Standard review</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$feedback['needs_admin_action']): ?>
                                        <a href="<?php echo url('index.php?action=teacher_feedback_forward&id=' . $feedback['feedback_id']); ?>" 
                                           class="btn btn-secondary btn-sm flex items-center justify-center gap-1 hover:bg-slate-800"
                                           onclick="return confirm('Are you sure you want to forward this feedback to System Admin for action?');">
                                            <i class="fa-solid fa-flag text-red-500"></i>
                                            Forward to Admin
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                                            <i class="fa-solid fa-check text-slate-400"></i>
                                            Sent
                                        </button>
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
