<?php
// views/parent/book_slot.php
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Slot Selector / Teacher Configuration -->
        <div class="content-card lg:col-span-1">
            <h3 class="card-title">Setup PTM Meeting Details</h3>
            
            <form action="<?php echo url('index.php'); ?>" method="GET" class="space-y-4">
                <input type="hidden" name="action" value="parent_book_slot">
                
                <div class="form-group">
                    <label for="event_id" class="form-label">Available PTM Calendar Event</label>
                    <select name="event_id" id="event_id" class="form-control form-select" required>
                        <option value="">-- Choose PTM Event --</option>
                        <?php foreach ($ptmEvents as $e): ?>
                            <option value="<?php echo $e['event_id']; ?>" <?php echo $selectedEventId == $e['event_id'] ? 'selected' : ''; ?>>
                                <?php echo date('M d, Y', strtotime($e['event_date'])); ?> 
                                (<?php echo date('h:i A', strtotime($e['start_time'])); ?> - <?php echo date('h:i A', strtotime($e['end_time'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="teacher_id" class="form-label">Select Instructor / Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-control form-select" required>
                        <option value="">-- Choose Teacher --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t['user_id']; ?>" <?php echo $selectedTeacherId == $t['user_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['name']); ?> (<?php echo htmlspecialchars($t['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary btn-block">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Search Available Slots
                </button>
            </form>
        </div>

        <!-- Available Times List Cards -->
        <div class="content-card lg:col-span-2">
            <h3 class="card-title">Select Available Time Slot</h3>

            <?php if (!$selectedTeacherId || !$selectedEventId): ?>
                <div class="text-center py-12 text-slate-500">
                    <i class="fa-regular fa-clock text-5xl text-slate-700 block mb-3"></i>
                    Configure the PTM event date and instructor to query available scheduling times.
                </div>
            <?php else: ?>
                <form action="<?php echo url('index.php?action=parent_book_slot'); ?>" method="POST" class="needs-validation">
                    <input type="hidden" name="teacher_id" value="<?php echo $selectedTeacherId; ?>">
                    <input type="hidden" name="slot_time" id="selected_slot_time" required>
                    
                    <p class="text-sm text-slate-400 mb-4">
                        PTM Schedule on <strong><?php echo date('F d, Y', strtotime($event['event_date'])); ?></strong> 
                        with teacher: <strong class="text-white"><?php echo htmlspecialchars(User::findById($selectedTeacherId)['name']); ?></strong>.
                    </p>

                    <div class="booking-grid">
                        <?php foreach ($availableSlots as $slot): ?>
                            <div class="slot-card <?php echo $slot['is_booked'] ? 'taken' : ''; ?>" 
                                 data-raw="<?php echo $slot['time_raw']; ?>" 
                                 <?php echo $slot['is_booked'] ? '' : 'onclick="selectSlot(this)"'; ?>>
                                <?php echo $slot['time_label']; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" id="book-confirm-btn" class="btn btn-primary btn-block mt-8" style="display: none;">
                        <i class="fa-solid fa-calendar-check"></i>
                        Confirm PTM Slot Booking
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function selectSlot(element) {
    // Deselect all active cards
    const cards = document.querySelectorAll(".slot-card");
    cards.forEach(c => c.classList.remove("selected"));

    // Select this card
    element.classList.add("selected");

    // Set value in hidden input field
    const rawVal = element.getAttribute("data-raw");
    document.getElementById("selected_slot_time").value = rawVal;

    // Show booking button
    document.getElementById("book-confirm-btn").style.display = "block";
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
