<?php
// views/admin/slots.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['assign_success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($_SESSION['assign_success']); unset($_SESSION['assign_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Manage Available / Custom Time Slots -->
        <div class="content-card xl:col-span-2">
            <div class="card-title">
                <span>Available PTM Time Slots</span>
                <button class="btn btn-primary btn-sm" data-open-modal="add-slot-modal">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Add Custom Slot
                </button>
            </div>

            <div class="mb-4">
                <input type="text" class="form-control table-search" data-table="slots-list-table" placeholder="Filter time slots by teacher or time...">
            </div>

            <div class="table-responsive">
                <table class="table" id="slots-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Teacher</th>
                            <th>Slot Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($slots)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-slate-400">No time slots created yet. Set up a PTM event or add a custom slot.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): ?>
                                <tr>
                                    <td>#SLT-00<?php echo $slot['slot_id']; ?></td>
                                    <td class="font-semibold text-white"><?php echo htmlspecialchars($slot['teacher_name']); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo date('M d, Y @ h:i A', strtotime($slot['slot_time'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $slot['duration']; ?> minutes</td>
                                    <td>
                                        <?php if ($slot['status'] === 'Available'): ?>
                                            <span class="badge badge-success">Available</span>
                                        <?php elseif ($slot['status'] === 'Booked'): ?>
                                            <span class="badge badge-info">Booked</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Blocked</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button class="btn btn-secondary btn-sm edit-slot-btn" 
                                                    data-id="<?php echo $slot['slot_id']; ?>" 
                                                    data-teacher="<?php echo $slot['teacher_id']; ?>" 
                                                    data-time="<?php echo date('Y-m-d\TH:i', strtotime($slot['slot_time'])); ?>" 
                                                    data-duration="<?php echo $slot['duration']; ?>"
                                                    data-status="<?php echo $slot['status']; ?>">
                                                <i class="fa-solid fa-pen text-indigo-400"></i>
                                            </button>
                                            <a href="<?php echo url('index.php?action=admin_slot_delete&id=' . $slot['slot_id']); ?>" 
                                               class="btn btn-secondary btn-sm hover:bg-red-950" 
                                               onclick="return confirm('Are you sure you want to delete this slot?');">
                                                <i class="fa-solid fa-trash text-red-500"></i>
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

        <!-- Booked Meetings & Teacher Reassignment -->
        <div class="content-card xl:col-span-1">
            <h3 class="card-title">Booked Meetings & Assignments</h3>
            <p class="text-xs text-slate-500 mb-4">View scheduled parent bookings and reassign slots to other teachers as necessary.</p>

            <div class="space-y-4">
                <?php if (empty($bookedMeetings)): ?>
                    <div class="text-center py-8 text-slate-500 text-sm">
                        <i class="fa-solid fa-calendar-xmark text-3xl text-slate-700 block mb-2"></i>
                        No meetings have been booked by parents yet.
                    </div>
                <?php else: ?>
                    <?php foreach ($bookedMeetings as $meeting): ?>
                        <div class="p-4 rounded-xl bg-slate-900 bg-opacity-40 border border-slate-800 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="badge badge-success text-[10px] mb-1">Booked</span>
                                    <h4 class="font-bold text-white text-sm"><?php echo htmlspecialchars($meeting['parent_name']); ?></h4>
                                    <p class="text-xs text-slate-400">Meeting #PTM-00<?php echo $meeting['meeting_id']; ?></p>
                                </div>
                                <span class="text-xs text-slate-400 font-mono"><?php echo date('M d, h:i A', strtotime($meeting['slot_time'])); ?></span>
                            </div>

                            <div class="p-2.5 rounded bg-slate-950 bg-opacity-50 border border-slate-850 text-xs">
                                <span class="text-slate-500 block">CURRENT ASSIGNED TEACHER:</span>
                                <strong class="text-indigo-300"><?php echo htmlspecialchars($meeting['teacher_name']); ?></strong>
                            </div>

                            <!-- Reassign Form -->
                            <form action="<?php echo url('index.php?action=admin_assign_teacher'); ?>" method="POST" class="flex gap-2">
                                <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                                <select name="teacher_id" class="form-control text-xs py-1.5" required>
                                    <option value="">-- Reassign Teacher --</option>
                                    <?php foreach ($teachers as $t): ?>
                                        <option value="<?php echo $t['user_id']; ?>" <?php echo $t['user_id'] == $meeting['teacher_id'] ? 'disabled' : ''; ?>>
                                            <?php echo htmlspecialchars($t['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm px-3" title="Save Assignment">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Custom Slot -->
<div class="modal" id="add-slot-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Custom PTM Slot</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_slot_create'); ?>" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="new_slot_teacher" class="form-label">Instructor / Teacher</label>
                <select name="teacher_id" id="new_slot_teacher" class="form-control form-select" required>
                    <option value="">-- Choose Teacher --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t['user_id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="new_slot_time" class="form-label">Slot Date & Time</label>
                <input type="datetime-local" name="slot_time" id="new_slot_time" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="new_slot_duration" class="form-label">Duration (Minutes)</label>
                <input type="number" name="duration" id="new_slot_duration" class="form-control" value="15" min="5" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Publish Custom Slot</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Slot -->
<div class="modal" id="edit-slot-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Time Slot</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_slot_edit'); ?>" method="POST" class="needs-validation">
            <input type="hidden" name="slot_id" id="edit_slot_id">
            
            <div class="form-group">
                <label for="edit_slot_teacher" class="form-label">Instructor / Teacher</label>
                <select name="teacher_id" id="edit_slot_teacher" class="form-control form-select" required>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t['user_id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="edit_slot_time" class="form-label">Slot Date & Time</label>
                <input type="datetime-local" name="slot_time" id="edit_slot_time" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_slot_duration" class="form-label">Duration (Minutes)</label>
                <input type="number" name="duration" id="edit_slot_duration" class="form-control" min="5" required>
            </div>

            <div class="form-group">
                <label for="edit_slot_status" class="form-label">Status</label>
                <select name="status" id="edit_slot_status" class="form-control form-select" required>
                    <option value="Available">Available</option>
                    <option value="Booked">Booked</option>
                    <option value="Blocked">Blocked</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Update Slot Details</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtns = document.querySelectorAll(".edit-slot-btn");
    const editModal = document.getElementById("edit-slot-modal");

    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("edit_slot_id").value = btn.getAttribute("data-id");
            document.getElementById("edit_slot_teacher").value = btn.getAttribute("data-teacher");
            document.getElementById("edit_slot_time").value = btn.getAttribute("data-time");
            document.getElementById("edit_slot_duration").value = btn.getAttribute("data-duration");
            document.getElementById("edit_slot_status").value = btn.getAttribute("data-status");
            editModal.classList.add("active");
        });
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
