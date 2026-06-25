<?php
// views/admin/feedback.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <div class="content-card">
        <div class="card-title">
            <span>System-Wide Parent Feedback Logs</span>
            <input type="text" class="form-control table-search max-w-xs" data-table="feedback-log-table" placeholder="Filter reviews...">
        </div>

        <div class="table-responsive">
            <table class="table" id="feedback-log-table">
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>PTM Date</th>
                        <th>Parent Client</th>
                        <th>Assigned Teacher</th>
                        <th>Rating Stars</th>
                        <th>Comments</th>
                        <th>Escalation Status</th>
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
                            <tr class="<?php echo $feedback['needs_admin_action'] ? 'bg-red-950 bg-opacity-20' : ''; ?>">
                                <td>#FB-00<?php echo $feedback['feedback_id']; ?></td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($feedback['slot_time'])); ?>
                                </td>
                                <td class="font-semibold text-white"><?php echo htmlspecialchars($feedback['parent_name']); ?></td>
                                <td class="font-semibold text-indigo-300"><?php echo htmlspecialchars($feedback['teacher_name']); ?></td>
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
                                <td class="text-slate-350 text-sm max-w-xs md:max-w-md break-words">
                                    <?php echo htmlspecialchars($feedback['comments']); ?>
                                </td>
                                <td>
                                    <?php if ($feedback['needs_admin_action']): ?>
                                        <span class="badge badge-danger animate-pulse">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                            Needs Admin Action
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Standard Logged</span>
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
