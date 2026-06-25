<?php
// views/teacher/dashboard.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <!-- Meeting List Card -->
    <div class="content-card">
        <div class="card-title">
            <span>Your Assigned PTM Meeting Schedule</span>
            <span class="text-sm font-semibold text-slate-400">Assigned parents slot bookings.</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Meeting ID</th>
                        <th>Parent Client</th>
                        <th>Student Name & Reg No</th>
                        <th>PTM Slot Time</th>
                        <th>Duration</th>
                        <th>Google Meet Link</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($meetings)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-slate-400 py-8">
                                <i class="fa-regular fa-calendar-minus text-4xl text-slate-600 block mb-2"></i>
                                No scheduled meeting sessions found for your profile.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td>#PTM-00<?php echo $meeting['meeting_id']; ?></td>
                                <td class="font-semibold text-white"><?php echo htmlspecialchars($meeting['parent_name']); ?></td>
                                <td>
                                    <div class="font-semibold text-indigo-300"><?php echo htmlspecialchars($meeting['student_name'] ?? 'N/A'); ?></div>
                                    <div class="text-xs text-slate-350">Student DB ID: <strong class="text-slate-200"><?php echo $meeting['student_id'] ?? 'N/A'; ?></strong></div>
                                    <div class="text-xs text-slate-400">Reg No: <?php echo htmlspecialchars($meeting['registration_no'] ?? 'N/A'); ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo date('M d, Y @ h:i A', strtotime($meeting['slot_time'])); ?>
                                    </span>
                                </td>
                                <td><?php echo $meeting['duration']; ?> Mins</td>
                                <td>
                                    <?php if ($meeting['meet_link']): ?>
                                        <div class="copy-link-box">
                                            <input type="text" id="meet_link_<?php echo $meeting['meeting_id']; ?>" value="<?php echo htmlspecialchars($meeting['meet_link']); ?>" readonly>
                                            <button class="copy-link-btn" data-target="meet_link_<?php echo $meeting['meeting_id']; ?>">Copy</button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-500">Pending Link</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($meeting['status'] === 'Scheduled'): ?>
                                        <a href="<?php echo url('index.php?action=teacher_conduct_meeting&id=' . $meeting['meeting_id']); ?>" 
                                           class="btn btn-primary btn-sm flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-video"></i>
                                            Join & Conduct
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo $meeting['status']; ?></span>
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
